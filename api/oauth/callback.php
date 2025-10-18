<?php 

require_once __DIR__ . '/../../utils/oauth.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/oauth_accounts.php';
require_once __DIR__ . '/../../queries/accounts.php';
require_once __DIR__ . '/../../config/config.php';

session_start();

$provider = getOAuthProvider();
$conn = getDBConnection();

/***
 * Note
 * Oauth is currently only used for basic users
 */

/***
 * TO DO: 
 * Refactor this code to separate logic into service files
 * Separate the concerns of user authentication and profile retrieval
 */
if($_GET['code'] && $_GET['state'] === $_SESSION['oauth2state']) {
    $decodedState = json_decode($_GET["state"], true);
    $intendedRole = $decodedState['role'];
    $redirectUrl = null;
    $user = null;
    $roleProfile = null;

    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch (Exception $e) {
        echo "Error: Authentication failed. Grant is invalid or expired.";
        exit;
    }

    $resourceOwner = $provider->getResourceOwner($token);
    $oauthEmail = $resourceOwner->getEmail();
    $oauthId = $resourceOwner->getId();
    $oauthLastName = $resourceOwner->getLastName();
    $oauthFirstName = $resourceOwner->getFirstName();

    $oauthUser = getOauthAccountWithOpenId($conn, $resourceOwner->getId());

    if(empty($oauthUser)) {
        $userAcc = [
            "email" => $oauthEmail,
            "password" => null,
            "lastName" => $oauthLastName,
            "firstName" => $oauthFirstName,
            "role" => $intendedRole
        ];
        
        $oauthAcc = [
            "oauthUserId" => $oauthId,
            "provider" => "google",
            "accessToken" => $token->getToken(),
            "refreshToken" => $token->getRefreshToken(),
            "tokenExpiry" => date('Y-m-d H:i:s', $token->getExpires())
        ];

        try {
            $conn->autocommit(FALSE);
            $conn->begin_transaction();
            createUserAccount($conn, $userAcc);
            $newUserId = $conn->insert_id;
            $oauthAcc['userId'] = $newUserId;
            createOauthAccount($conn, $oauthAcc);
            $conn->commit();
            $user = getUserAccById($conn, $newUserId);
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error creating user account: " . $e->getMessage();
            exit;
        }

    } else {

        $userId = $oauthUser['user_id'];
        $user = getUserAccById($conn, $userId);

        if(empty($user)) {
            echo "Error: No account found.";
            exit;
        }

        if($user['role'] !== $intendedRole) {
            echo "Error: Unauthorized user.";
            exit;
        }        

    }
    echo "User is successfully authenticated using Google. Redirecting to maps page...";
    exit;

    // $redirectUrl = BASE_URL . "/redirect.php";
    // generateSession($user, $roleProfile);
    // redirectUser($redirectUrl);

} else {
    // if auth code is missing and session state is invalid 
    unset($_SESSION['oauth2state']);
    exit('Invalid authorization request.');
}

?>