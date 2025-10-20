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

?>