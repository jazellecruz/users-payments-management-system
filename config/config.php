<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_NAME']);

define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID']);
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET']);

define('BASE_URL', 'http://localhost/users-payments-management-system/');
define('OAUTH2_CALLBACK', 'api/oauth/callback.php');

define('MEDIA_STORE_API_KEY', $_ENV['MEDIA_STORE_API_KEY']);
define('MEDIA_STORE_NAME', $_ENV['MEDIA_STORE_NAME']);
define('MEDIA_STORE_SECRET', $_ENV['MEDIA_STORE_SECRET']);

define('EMAIL', $_ENV['EMAIL']);

?>