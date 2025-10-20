<?php   

require_once __DIR__ . '../../../db/db_conn.php';
require_once __DIR__ . '../../../utils/utils.php';
require_once __DIR__ . '../../../utils/auth.php';
require_once __DIR__ . '../../../queries/business.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'edit_profile_image') { 
        $mediaStore = getMediaStore();
        $conn = getDbConnection();

        $newProfileImage = is_uploaded_file($_FILES['profile_image']['tmp_name']) ? $_FILES['profile_image'] : null;
        $businessRepId = sanitizeData($conn, $_POST['business_rep_id']);

        try{
            $newFileName = 'BUSREP-PROFILE-' . strtoupper(generateNanoId(10));

            $uploadedImg = $mediaStore->uploadApi()->upload($newProfileImage['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/businesses'
            ]);

            $result = updateBusinessRepProfileImg($conn, $businessRepId, $uploadedImg['secure_url']);

            if($result) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }
            
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'edit_personal_info') {
        $conn = getDbConnection();

        $businessRepId = sanitizeData($conn, $_POST['business_rep_id']);
        $firstName = sanitizeData($conn, $_POST['first_name']);
        $lastName = sanitizeData($conn, $_POST['last_name']);
        $middleName = sanitizeData($conn, $_POST['middle_name']);
        $extName = sanitizeData($conn, $_POST['ext_name']);
        $birthDate = sanitizeData($conn, $_POST['birth_date']);
        $gender = sanitizeData($conn, $_POST['gender']);
        $address = sanitizeData($conn, $_POST['address']);
        $contactNum = sanitizeData($conn, $_POST['contact_num']);
        $altEmail = sanitizeData($conn, $_POST['alt_email']);

        $repData = [
            'business_rep_id' => $businessRepId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'ext_name' => $extName,
            'birth_date' => $birthDate,
            'gender' => $gender,
            'user_address' => $address,
            'active_phone_number' => $contactNum,
            'alternative_email' => $altEmail
        ];

        try {
            $result = updateBusinessRepInfo($conn, $repData);

            if($result) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>