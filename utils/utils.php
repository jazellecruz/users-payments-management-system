<?php 
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '../../config/config.php';


function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function sanitizeData($conn, $data) {
    $data = trim($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

function getMediaStore() {
    $store = new \Cloudinary\Cloudinary([
        'cloud' => [
            'cloud_name' => MEDIA_STORE_NAME,
            'api_key'  => MEDIA_STORE_API_KEY,
            'api_secret' => MEDIA_STORE_SECRET,
            'url' => [
                'secure' => true
            ]
        ]
    ]);

    return $store;
}

function generateNanoId($size = 21) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nanoIdClient = new Hidehalo\Nanoid\Client;
    return $nanoIdClient->formattedId($chars, $size);
}

function generateUniqueFileName($file){
    $fileTypeExtensions = [
        'image/jpeg' => '.jpg',
        'image/jpg'  => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'image/webp' => '.webp',
        'image/bmp'  => '.bmp',
        'image/svg+xml' => '.svg',
        'image/tiff' => '.tiff',
        'application/pdf' => '.pdf',
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/vnd.ms-excel' => '.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.ms-powerpoint' => '.ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx',
        'text/plain' => '.txt',
        'text/csv' => '.csv',
        'application/zip' => '.zip',
        'application/x-rar-compressed' => '.rar',
        'application/x-7z-compressed' => '.7z',
        'application/gzip' => '.gz',
        'application/x-tar' => '.tar',
        'audio/mpeg' => '.mp3',
        'audio/wav' => '.wav',
        'audio/ogg' => '.ogg',
        'audio/mp4' => '.m4a',
        'video/mp4' => '.mp4',
        'video/x-msvideo' => '.avi',
        'video/x-ms-wmv' => '.wmv',
        'video/mpeg' => '.mpeg',
        'video/quicktime' => '.mov',
        'video/webm' => '.webm',
        'video/x-flv' => '.flv',
        'application/json' => '.json',
        'application/xml' => '.xml',
    ];

    $name = $file['name'];
    $extension = $file['type'];
    return $name . '-' . generateNanoId(18) . ($fileTypeExtensions[$extension] ?? 'bin');
}

function maskEmail($email) {
    $charPos = strpos($email, '@');
    $cut = substr($email, 0, $charPos);
    $cutoffLength = strlen(substr($email, 0, $charPos)) - 3;
    $maskedEmail = substr($email, 0, 3) . str_repeat('*', $cutoffLength) . substr($email, $charPos);
    return $maskedEmail;
}

function getPredisClient($config) {
    $client = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => REDIS_HOST,
        'port'   => REDIS_PORT,
        'password' => REDIS_PASSWORD,
        'prefix' => $config['prefix'] ?? ''
    ]);

    return $client;
}

function getResPassPrefix() {
    return RES_PASSWORD_PREFIX . ":";
}

function generateResetPasswordEmail($firstName, $resetLink) {
    $emailBody = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style type='text/css'>
                h2 {
                    color: #3F562C;
                    font-family: 'Optima', sans-serif;
                }
                p {
                    font-size: 14px;
                    color: #333;
                    font-family: 'Optima', sans-serif;
                }
                .email-container {
                    font-family: 'Optima', sans-serif;
                    text-align: left;
                    padding: 60px 20px; 
                    background-color: #faf7e3ff;
                }

                .email-message-container {
                    max-width: 600px;
                    margin: auto;
                    background-color: #fff;
                    border: 0.5px solid rgba(230, 230, 230, 1);
                    border-radius: 10px;
                }

                .email-header {
                    background-color: #3F562C;
                    height: 60px;
                    margin-bottom: 20px;
                    border-radius: 10px 10px 0 0;
                }

                .email-body {
                    padding: 0 20px 20px 20px;
                }

                .reset-btn{
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #3F562C;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 10px 0;
                    font-size: 14px;
                    color: #fff;
                    border: none;
                    cursor: pointer;
                }

                .journeolink-team-text {
                    font-weight: 600;
                    font-style: italic;
                    color: #3F562C;
                    padding-top: 20px;
                }

                @media only screen and (max-width: 1000px){
                    .email-container {
                        padding: 40px 10px; 
                    }

                    .email-message-container {
                        width: 100%;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-message-container'>
                    <div class='email-header'>
                    </div>
                    <div class='email-body'>
                        <h2>Forgot Your Password?</h2>
                        <p>Hi, ". htmlspecialchars($firstName) ." 👋 We received a request to reset your password for your Journeolink account.</p>
                        <p>To reset your password, please click the link below:</p>
                        <a  href='". htmlspecialchars($resetLink) ."'>
                            <button class='reset-btn'>Reset Password</button>
                        </a>
                        <p>If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>
                        <p>This link will expire in 15 minutes for your security.</p>
                        <p class='journeolink-team-text'>Journeolink Team</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
    ";

    return $emailBody;
}

function hashResetPassSessionId($sessionId) {
    return hash_hmac(REDIS_HASH_ALGO, $sessionId, REDIS_HASH_KEY);
}

function hashVerificationCode($code) {
    return hash_hmac('sha256', $code, VERIFICATION_CODE_HASH_KEY);
}

function generateVerificationCode() {
    return generateNanoId(VERIFICATION_CODE_LENGTH);
}

function generateVerificationEmail($emailData) {
    $emailBody = "
                <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style type='text/css'>
                h2 {
                    color: #3F562C;
                    font-family: 'Optima', sans-serif;
                }
                p {
                    font-size: 14px;
                    color: #333;
                    font-family: 'Optima', sans-serif;
                }
                .email-container {
                    font-family: 'Optima', sans-serif;
                    text-align: left;
                    padding: 60px 20px; 
                    background-color: #f1ffe5ff;
                }

                .email-message-container {
                    max-width: 600px;
                    margin: auto;
                    background-color: #fff;
                    border: 0.5px solid rgba(230, 230, 230, 1);
                    border-radius: 10px;
                }

                .email-icon {
                    width: 80px;
                    padding-top: 20px;
                }

                .email-body {
                    padding: 0 20px 20px 20px;
                }

                .reset-btn{
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #3F562C;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 10px 0;
                    font-size: 14px;
                    color: #fff;
                    border: none;
                    cursor: pointer;
                }

                .journeolink-team-text {
                    font-weight: 600;
                    font-style: italic;
                    color: #3F562C;
                    padding-top: 20px;
                }

                @media only screen and (max-width: 1000px){
                    .email-container {
                        padding: 40px 10px; 
                    }

                    .email-message-container {
                        width: 100%;
                    }
                }
                
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-message-container'>
                    <!-- <div class='email-header'>
                    </div> -->
                    <div class='email-body'>
                        <img src='../public/images/verify-email-icon-v2.png' class='email-icon' alt='' srcset=''>
                        <h2>Please Verify Your Email</h2>
                        <p style='line-height: 1.7;'>Hi, ". htmlspecialchars($emailData['userName']) ." 👋 Thank you for signing up on Journeolink! To access your account and continue onboarding, we need to verify your email address.</p>
                        <p>To verify your email address, please click the link below:</p>
                        <a  href='". htmlspecialchars($emailData['resetLink']) ."'>
                            <button class='reset-btn'>Verify Email</button>
                        </a>
                        <p>This link will expire in <strong>15 minutes</strong>, so please make sure to verify your email soon.</p>
                        <p>If you did not sign up for an account, please ignore this email.</p>
                        <p class='journeolink-team-text'>Journeolink Team</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
    "; 

    return $emailBody;
}
?>