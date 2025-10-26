<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../db/db_conn.php';
require_once '../../../utils/auth.php';
require_once '../../../utils/utils.php';

$conn = getDbConnection();

if (!isset($_SESSION['user_id'])) {
    redirectUser('../auth/driver_login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$check = $conn->prepare("
  SELECT status FROM driver_applications 
  WHERE user_id = ? 
  ORDER BY applied_at DESC LIMIT 1
");
$check->bind_param("i", $_SESSION['user_id']);
$check->execute();
$check->bind_result($latestStatus);
$check->fetch();
$check->close();

if ($latestStatus === 'pending') {
    redirectUser('/users-payments-management-system/views/driver/dashboard/applications.php?error=pending');
    exit;
}



$first_name     = $_POST['first_name'] ?? '';
$middle_name    = $_POST['middle_name'] ?? '';
$last_name      = $_POST['last_name'] ?? '';
$ext_name = $_POST['ext_name'] ?? '';
$birth_date     = $_POST['birth_date'] ?? '';
$gender         = $_POST['gender'] ?? '';
$email          = $_POST['email'] ?? '';
$alt_email      = $_POST['alternative_email'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$license_number = $_POST['license_number'] ?? '';
$license_expiry = $_POST['license_expiry_date'] ?? '';
$user_address = $_POST['user_address'] ?? 'Not provided';




function uploadToCloud($field) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $store = getMediaStore();
    $uniqueName = generateUniqueFileName($_FILES[$field]);

    $result = $store->uploadApi()->upload(
        $_FILES[$field]['tmp_name'],
        ["public_id" => "driver_docs/" . $uniqueName]
    );

    return $result['secure_url'] ?? null;
}

$profile_photo = uploadToCloud('profile_photo');
$license_photo = uploadToCloud('license_photo');
$nbi_clearance = uploadToCloud('nbi_clearance');
$proof_of_address = uploadToCloud('proof_of_address');

function generateDriverAppId($length = 10) {
    $random = strtoupper(bin2hex(random_bytes($length)));
    $random = substr($random, 0, $length);
    return 'DRV-APP-' . $random;
}

$publicId = generateDriverAppId();


$stmt = $conn->prepare("
  INSERT INTO driver_applications (
  driver_app_public_id,
  user_id, first_name, middle_name, last_name, ext_name, birth_date, gender,
  user_address, alternative_email, active_phone_number, license_number, license_expiry_date,
  id_pic_img_url, license_img_url, nbi_clearance_img_url, proof_of_address_img_url,
  status, applied_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())

");



$stmt->bind_param(
  "sisssssssssssssss", 
  $publicId,
  $userId,
  $first_name,
  $middle_name,
  $last_name,
  $ext_name,
  $birth_date,
  $gender,
  $user_address,
  $alt_email,
  $contact_number,
  $license_number,
  $license_expiry,
  $profile_photo,
  $license_photo,
  $nbi_clearance,
  $proof_of_address
);









if ($stmt->execute()) {
    header("Location: /users-payments-management-system/views/driver/dashboard/applications.php?success=1");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
