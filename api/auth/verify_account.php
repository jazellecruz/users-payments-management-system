<?php 

session_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/accounts.php';
require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../utils/auth.php';


if($_SERVER['REQUEST_METHOD'] === 'GET') {

    // to do: set up bg jobs to clean up expired verification codes from db
    if((isset($_GET['verification_code']) && !empty($_GET['verification_code']))
        && (isset($_GET['email']) && !empty($_GET['email']))) 
    {
        $conn = getDBConnection();
        $verificationCode = $_GET['verification_code'];
        $email = $_GET['email'];
        
        try {
            $hashedVerificationCode = hashVerificationCode($verificationCode);

            $unverifiedUser = getUnverifiedUserByCodeAndEmail($conn, $email, $hashedVerificationCode);

            if(!$unverifiedUser) {
                throw new Exception("Invalid verification code.");
            }

            $currentDateTime = date('Y-m-d H:i:s');

            if($currentDateTime > $unverifiedUser['expires_at']) {
                throw new Exception("Verification code has expired. Please request a new verification email.");
            }

            // create user account by moving data from unverified_users to users table
            $conn->begin_transaction();
            $conn->autocommit(false); 
            $userData = [
                'email' => $unverifiedUser['email'],
                'password' => $unverifiedUser['password_hash'],
                'firstName' => $unverifiedUser['first_name'],
                'lastName' => $unverifiedUser['last_name'],
                'role' => $unverifiedUser['role']
            ];

            $newUser = createUserAccount($conn, $userData);
            $newUserId = $conn->insert_id;
            $newUser = getUserAccById($conn, $newUserId);

            if(!$newUser) {
                throw new Exception("Failed to create user account. Please try again.");
            }

            $deletedUnverifiedUser = deleteUnverifiedUserById($conn, $unverifiedUser['unverified_user_id']);

            // delete unverified user entry to make sure verification code can't be reused
            if(!$deletedUnverifiedUser) {
                throw new Exception("Failed to delete unverified user. Please try again.");
            }
            
            $conn->commit();
            
            // set user session and add redirect link depending on user role
            $_SESSION['user_id'] = $newUser['user_id'];
            $_SESSION['role'] = $newUser['role'];

            if($newUser['role'] === 'bus_rep') {
                $_SESSION['isForOnboarding'] = true; // users are automatically redirected to onboarding after verification
                $_SESSION['next_redirection'] = BASE_URL . '/views/business_rep/onboarding/onboarding.php';
                // incase user does not want to apply right away
                $_SESSION['alt_redirection'] = BASE_URL . '/views/business_rep/auth/business_rep_login.php';
            }

            if($newUser['role'] === 'driver') {
                $_SESSION['isForOnboarding'] = true; // users are automatically redirected to onboarding after verification
                $_SESSION['next_redirection'] = BASE_URL . '/views/driver/onboarding/onboarding.php';
                // incase user does not want to apply right away
                $_SESSION['alt_redirection'] = BASE_URL . '/views/driver/auth/driver_login.php'; 
            }

            if($newUser['role'] === 'basic') {
                // CHANGE REDIRECTION LINK OF BASIC USER
                $_SESSION['next_redirection'] = BASE_URL . '/views/auth/login.php';
            }

            $redirectLink = BASE_URL . '/views/auth/verification_success.php';
            redirectUser($redirectLink);
        } catch (Exception $e) {
            $conn->rollback(); 
            echo deleteUnverifiedUserById($conn, $unverifiedUser['unverified_user_id']);
            echo $e->getMessage();
        } 
    }

}
?>