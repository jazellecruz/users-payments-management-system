<?php 

session_start();

require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/accounts.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../services/email_service.php';
require_once __DIR__ . '/../../config/config.php';

$conn = getDBConnection();

/** to do: 
 * - separate service logic from controller logic 
**/
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'account_signup') {

        $verificationNoticeUrl = BASE_URL . '/views/auth/verification_notice.php';
        
        $firstName = sanitizeData($conn, $_POST['first_name']) ?? '';
        $lastName = sanitizeData($conn, $_POST['last_name']) ?? '';
        $email = sanitizeData($conn, $_POST['email']);
        $password = sanitizeData($conn, $_POST['password']);
        $role = sanitizeData($conn, $_POST['role']);

        // check first if user already exists in the database
        $existingUser = getUserByEmailAndRole($conn, $email, $role);

        if(!empty($existingUser)) {
            echo "User with this email already exists.";
            exit;
        }
        
        $hashedPassword = hashPassword($password);
        $verificationCode = generateVerificationCode();
        $hashedVerificationCode = hashVerificationCode($verificationCode);
        $expiresAt = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . ' +' . VERIFICATION_CODE_EXPIRY_LEN . ' minutes'));

        $user = [
            'email' => $email,
            'hashedPassword' => $hashedPassword,
            'role' =>  $role,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'verification_code' => $hashedVerificationCode,
            'expires_at' => $expiresAt
        ];

        $res = createUnverifiedUserAccount($conn, $user);

        if(!$res) {
            echo "Error creating account. Please try again.";
            exit;
        }

        // send verification email
        $verificationLink = BASE_URL . "/api/auth/verify_account.php?email=" . urlencode($email) . "&verification_code=" . urlencode($verificationCode);
        $emailBodyData = [
            'userName' => $firstName,
            'resetLink' => $verificationLink
        ];
        $emailBody = generateVerificationEmail($emailBodyData);
        $emailData = [
            'to' => $email,
            'subject' => 'Verify Your Email Address',
            'body' => $emailBody
        ];

        $config = [
            'isHTML' => true
        ];

        $res = sendEmail($emailData, $config);
        $returnUrl = null;

        if($role === 'bus_rep') $returnUrl = '../business_rep/auth/sign-up.php';
        if($role === 'driver') $returnUrl = '../driver/auth/sign-up.php';
        if($role === 'basic') $returnUrl = '../../views/auth/signup.php';

        $_SESSION['verification_return_url'] = $returnUrl;
        if($res) {
            // redirect user to verification notice page
            redirectUser($verificationNoticeUrl);
        } else {
            throw new Exception("Failed to send password reset email. Please try again later.");
        }

    }
}


?>