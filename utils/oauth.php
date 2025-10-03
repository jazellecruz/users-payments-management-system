<?php 

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use League\OAuth2\Client\Provider\Google;

function getOAuthProvider() {
    $callbackUrl = BASE_URL . OAUTH2_CALLBACK;
    $provider = new League\OAuth2\Client\Provider\Google([
        'clientId'     => GOOGLE_CLIENT_ID,
        'clientSecret' => GOOGLE_CLIENT_SECRET,
        'redirectUri'  => $callbackUrl,
    ]);
    return $provider;
}

?>