<?php 

require_once __DIR__ . '/../../utils/oauth.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/oauth_accounts.php';
require_once __DIR__ . '/../../queries/accounts.php';
require_once __DIR__ . '/../../config/config.php';

session_start();

/***
 * TO DO: 
 * - Refactor this code to separate logic into service files
 * - Separate the concerns of user authentication and profile retrieval
 * - fix generateSession function to avoid code repetition
 * - encase session in user variable
 */
if($_GET['code'] && $_GET['state'] === $_SESSION['oauth2state']) {
    $decodedState = json_decode($_GET["state"], true);
    $intendedRole = $decodedState['role'];

    try {
        if($intendedRole == 'bus_rep') handleOAuthBusRepUser();
        if($intendedRole == 'driver') handleOAuthDriverUser();
        if($intendedRole == 'basic') handleOauthBasicUser();
    } catch (Exception $e) {
        echo "Error: Authentication failed. Grant is invalid or expired.";
        exit;
    }
} else {
    // if auth code is missing and session state is invalid 
    unset($_SESSION['oauth2state']);
    exit('Invalid authorization request.');
}


// handle oauth for business rep user
function handleOAuthBusRepUser() {
    $provider = getOAuthProvider();
    $conn = getDBConnection();

    $decodedState = json_decode($_GET["state"], true);
    $intendedRole = $decodedState['role'];
    $userForOnboarding = $decodedState['forOnboarding'];
    $user = null;

    $redirectUrl = null;

    try{
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch(Exception $e) {
        throw new Exception("Error: Authentication failed. Grant is invalid or expired.");
    }

    try {
        $resourceOwner = $provider->getResourceOwner($token);
        $oauthEmail = $resourceOwner->getEmail();
        $oauthId = $resourceOwner->getId();
        $oauthLastName = $resourceOwner->getLastName();
        $oauthFirstName = $resourceOwner->getFirstName();
        $oauthAccImg = $resourceOwner->getAvatar();

        $oauthUser = getOauthAccountWithOpenId($conn, $resourceOwner->getId());

        if(empty($oauthUser)) {
        // if there is no existing oauth account, 
            $userAcc = [
                "email" => $oauthEmail,
                "password" => null,
                "lastName" => $oauthLastName,
                "firstName" => $oauthFirstName,
                "role" => $intendedRole,
                "acc_img_url" => $oauthAccImg
            ];
            
            $oauthAcc = [
                "oauthUserId" => $oauthId,
                "provider" => "google",
                "accessToken" => $token->getToken(),
                "refreshToken" => $token->getRefreshToken(),
                "tokenExpiry" => date('Y-m-d H:i:s', $token->getExpires())
            ];
  
            $conn->autocommit(FALSE);
            $conn->begin_transaction();

            createUserAccount($conn, $userAcc);
            $newUserId = $conn->insert_id;
            $oauthAcc['userId'] = $newUserId;

            createOauthAccount($conn, $oauthAcc);
            $conn->commit();
            $user = getUserAccById($conn, $newUserId);
        } 
        
        
        if(!empty($oauthUser)){
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

        $_SESSION["user_id"] = $user['user_id'];
        $_SESSION["role"] = $user['role'];
        $_SESSION["first_name"] = $user['first_name'];
        $_SESSION["last_name"] = $user['last_name'];
        $_SESSION["email"] = $user['email'];
        $_SESSION["acc_img_url"] = $user['acc_img_url'];
        $_SESSION["isForOnboarding"] = $userForOnboarding; // for when user needs to be redirected to onboarding page

        $redirectUrl = $userForOnboarding 
        ? "../../views/business_rep/onboarding/onboarding.php" 
        : "../../views/business_rep/dashboard/applications.php";

        redirectUser($redirectUrl);
    } catch(Exception $e) {
        $conn->rollback();
        throw new Exception("Error: Failed to create a new account using OAuth.");
    }
}

// handle oauth for driver user
function handleOAuthDriverUser() {
    $provider = getOAuthProvider();
    $conn = getDBConnection();

    $decodedState = json_decode($_GET["state"], true);
    $intendedRole = $decodedState['role'];
    $userForOnboarding = $decodedState['forOnboarding'];
    $user = null;


    $redirectUrl = null;

    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch(Exception $e) {
        throw new Exception("Error: Authentication failed. Grant is invalid or expired.");
    }

    try {
        $resourceOwner = $provider->getResourceOwner($token);
        $oauthEmail = $resourceOwner->getEmail();
        $oauthId = $resourceOwner->getId();
        $oauthLastName = $resourceOwner->getLastName();
        $oauthFirstName = $resourceOwner->getFirstName();
        $oauthAccImg = $resourceOwner->getAvatar();

        $oauthUser = getOauthAccountWithOpenId($conn, $resourceOwner->getId());

        if(empty($oauthUser)) {
        // if there is no existing oauth account, 
            $userAcc = [
                "email" => $oauthEmail,
                "password" => null,
                "lastName" => $oauthLastName,
                "firstName" => $oauthFirstName,
                "role" => $intendedRole,
                "acc_img_url" => $oauthAccImg
            ];
            
            $oauthAcc = [
                "oauthUserId" => $oauthId,
                "provider" => "google",
                "accessToken" => $token->getToken(),
                "refreshToken" => $token->getRefreshToken(),
                "tokenExpiry" => date('Y-m-d H:i:s', $token->getExpires())
            ];
  
            $conn->autocommit(FALSE);
            $conn->begin_transaction();

            createUserAccount($conn, $userAcc);
            $newUserId = $conn->insert_id;
            $oauthAcc['userId'] = $newUserId;

            createOauthAccount($conn, $oauthAcc);
            $conn->commit();
            $user = getUserAccById($conn, $newUserId);
        } 
        
        
        if(!empty($oauthUser)){
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

        $_SESSION["user_id"] = $user['user_id'];
        $_SESSION["role"] = $user['role'];
        $_SESSION["first_name"] = $user['first_name'];
        $_SESSION["last_name"] = $user['last_name'];
        $_SESSION["email"] = $user['email'];
        $_SESSION["acc_img_url"] = $user['acc_img_url'];
        $_SESSION["isForOnboarding"] = $userForOnboarding; // for when user needs to be redirected to onboarding page

        $redirectUrl = $userForOnboarding 
        ? "../../views/driver/onboarding/onboarding.php" 
        : "../../views/driver/dashboard/applications.php";

        redirectUser($redirectUrl);
    } catch(Exception $e) {
        $conn->rollback();
        throw new Exception("Error: Failed to create a new account using OAuth.");
    }
}

// handle oauth for basic user
function handleOauthBasicUser() {
    $provider = getOAuthProvider();
    $conn = getDBConnection();

    $decodedState = json_decode($_GET["state"], true);
    $intendedRole = $decodedState['role'];
    $user = null;
    $userProfile = null;

    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch(Exception $e) {
        throw new Exception("Error: Authentication failed. Grant is invalid or expired.");
    }

    try {
        $resourceOwner = $provider->getResourceOwner($token);
        $oauthEmail = $resourceOwner->getEmail();
        $oauthId = $resourceOwner->getId();
        $oauthLastName = $resourceOwner->getLastName();
        $oauthFirstName = $resourceOwner->getFirstName();
        $oauthAccImg = $resourceOwner->getAvatar();

        $oauthUser = getOauthAccountWithOpenId($conn, $resourceOwner->getId());

        if(empty($oauthUser)) {
            // if there is no existing oauth account, 
            $userAcc = [
                "email" => $oauthEmail,
                "password" => null,
                "lastName" => $oauthLastName,
                "firstName" => $oauthFirstName,
                "role" => $intendedRole,
                "acc_img_url" => $oauthAccImg
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

        }

        if(!empty($oauthUser)){
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

        $_SESSION["user_id"] = $user['user_id'];
        $_SESSION["role"] = $user['role'];
        $_SESSION["first_name"] = $user['first_name'];
        $_SESSION["last_name"] = $user['last_name'];
        $_SESSION["email"] = $user['email'];
        $_SESSION["acc_img_url"] = $user['acc_img_url'];

        echo "User is successfully authenticated using Google. Redirecting to maps page...";
        exit;
    } catch (Exception $e) {
        throw new Exception("Error: Failed to create a new account using OAuth.");
    }
}

?>