<?php 

session_start();

require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/accounts.php';
require_once __DIR__ . '/../../services/email_service.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'request_reset_password') {
        try {
            $conn = getDbConnection();
            $redisClient = getPredisClient(["prefix" => getResPassPrefix()]);

            $email = sanitizeData($conn, $_POST['email']);

            $user = getUserAccByEmail($conn, $email);

            if(!$user) {
                throw new Exception("A user with that email does not exist.");
            }

            $userEmail = $user['email'];
            $userFirstName = $user['first_name'];
            $userId = $user['user_id'];

            // Generate a unique session ID for password reset session
            $resPassSessId = generateNanoId(21);
            $hashedSessId = hashResetPassSessionId($resPassSessId);

            $sessData = [
                'session_id' => $hashedSessId,
                'email' => $userEmail,
                'user_id' => $userId,
                'created_at' => time(),
                'isUsed' => 0 // this session is one time use only, set to true once used
            ];

            $sessionKey = getResPassPrefix() . $hashedSessId;

            $redisClient->hmset($sessionKey, $sessData);
            $redisClient->expire($sessionKey, RES_PASS_SESS_LIFETIME); // key will only be "alive" for 15 minutes

            $resetPassSessUrl = BASE_URL . "views/auth/reset_password.php?session_id=" . urlencode($resPassSessId);

            $emailMsg = generateResetPasswordEmail($userFirstName, $resetPassSessUrl);

            $emailData = [
                'to' => $userEmail,
                'subject' => 'Password Reset Request',
                'body' => $emailMsg
            ];

            $config = [
                'isHTML' => true
            ];

            $res = sendEmail($emailData, $config);

            if($res) {
                header('Content-Type: application/json');
                http_response_code(200);
                $response = [
                    'status' => 'success',
                    'userMsg' => 'A reset password link has been sent to your email address. Please check your inbox.',
                ];
                echo json_encode($response);
            } else {
                throw new Exception("Failed to send password reset email. Please try again later.");
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            $response = [
                'status' => 'error',
                'errMsg' => $e->getMessage()
            ];
            echo json_encode($response);
            exit;
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'perform_reset_password') {
        
        try {
            $conn = getDbConnection();
            $redisClient = getPredisClient(["prefix" => getResPassPrefix()]);
            $sessionId = $_POST['session_id'];
            $email = $_POST['email'];
            $newPassword = $_POST['new_password'];

            // check again if session is valid for added security
            $hashedSessId = hashResetPassSessionId($sessionId);
            $sessionKey = getResPassPrefix() . $hashedSessId;

            $sessionData = $redisClient->hgetall($sessionKey);

            if(empty($sessionData) || $sessionData['isUsed'] === '1'|| !($sessionData['email'] == $email)) {
                throw new Exception("Invalid or expired password reset session.");
            }

            // check if user exists
            $user = getUserAccByEmail($conn, $email);

            if(empty($user) || !($user['user_id'] == $sessionData['user_id'])) {
                throw new Exception("User is invalid or not found.");
            }

            // update user password
            $hashedNewPass = hashPassword($newPassword);
            $res = updateAccountPassword($conn, $user['user_id'], $hashedNewPass);

            if(!$res) {
                throw new Exception("Failed to update password. Please try again later.");
            }

            // delete session since it is one-time use only
            $redisClient->del($sessionKey);

            $redirectLink = null;

            /***
             * NOTE: CODE BELOW IS ERROR-PRONE SINCE A SINGLE ACCOUNT IS TIED TO ONE ROLE 
             * IN THE DATABASE (I.E. ROLES ARE NOT STORED IN A SEPARATE TABLE), THIS CODE
             * WILL BREAK IF USER USED THE SAME EMAUIL FOR MULTIPLE ROLES (BASIC, DRIVER, BUS REP).
             * ALSO USER SHOULD BE REDIRECTED FROM THE BACKEND NOT FROM THE FRONTEND.
             * refactoring this is fvking needed in the future
             */
            if($user['role'] === 'basic') $redirectLink = 'login.php';
            if($user['role'] === 'driver') $redirectLink = '../driver/auth/driver_login.php';
            if($user['role'] === 'bus_rep') $redirectLink = '../business_rep/auth/business_rep_login.php';

            
            $_SESSION['reset_pass_status'] = [
                'status' => 'success',
                'userMsg' => 'Your password has been reset successfully.',
                'redirectLink' => $redirectLink
            ];
            redirectUser("../../views/auth/reset_pass_status.php");
            exit;
        } catch(Exception $e) {
            // delete session in case of error to prevent reuse
            if(isset($sessionKey)) $redisClient->del($sessionKey);

            $_SESSION['reset_pass_status'] = [
                'status' => 'error',
                'userMsg' => 'Your password has not been reset successfully. Your session is invalid or has expired. Please try again. ',
            ];
            redirectUser("../../views/auth/reset_pass_status.php");
            exit;
        }
    }
}

?>