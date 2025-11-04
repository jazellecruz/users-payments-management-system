<?php 
require_once __DIR__ . '/../../utils/oauth.php';

session_start();

$callbackUrl = BASE_URL . OAUTH2_CALLBACK;
$provider = getOAuthProvider();

if (!empty($_GET['error'])) {
    // This error might arise if user denies access to applicaition
    exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
}

// Redirect user to authorization URL if no code is present
if (empty($_GET['code'])) {
    $role = $_GET['role'];
    $authUrl = $provider->getAuthorizationUrl([
        "state" => json_encode([
            "role" => $role,
            "forOnboarding" => isset($_GET['for-onboarding']) ? true : false 
            ]) 
    ]);
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;
} 
 
?>