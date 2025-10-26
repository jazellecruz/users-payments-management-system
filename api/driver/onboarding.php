<?php 

require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../queries/driver.php';

// Utility function to generate driver app IDs
function generateDriverAppId($length = 10) {
    $random = strtoupper(bin2hex(random_bytes($length)));
    $random = substr($random, 0, $length);
    return 'DRV-APP-' . $random;
}

$conn = getDbConnection();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'driver_onboarding') {
        $store = getMediaStore();

        $userAccId = sanitizeData($conn, $_POST['user_acc_id']);

        $firstName = sanitizeData($conn, $_POST['first_name']);
        $lastName = sanitizeData($conn, $_POST['last_name']);   
        $middleName = sanitizeData($conn, $_POST['middle_name']); 
        $extName = sanitizeData($conn, $_POST['ext_name']) ?? '';  
        $altEmail = sanitizeData($conn, $_POST['alt_email']);
        $address = sanitizeData($conn, $_POST['address']);
        $birthDate = sanitizeData($conn, $_POST['birthdate']);
        $gender = strtolower(sanitizeData($conn, $_POST['gender']));
        $contactNumber = sanitizeData($conn, $_POST['contact_number']);
        $licenseNumber = sanitizeData($conn, $_POST['license_num']);
        $licenseExpiry = sanitizeData($conn, $_POST['license_expiry_date']);
        $agreedToTerms = isset($_POST['terms_conds_checkbox']) == 'on' ? true : false;

        $files = array(
            'nbi_clearance' => [
                'doc_type' => 'nbi_clearance',
                ...$_FILES['nbi_clearance']
            ],
            'license_photo' => [
                'doc_type' => 'license_photo',
                ...$_FILES['license_photo']
            ],
            'id_picture' => [
                'doc_type' => 'id_picture',
                ...$_FILES['id_picture']
            ],
            'address_proof_photo' => [
                'doc_type' => 'address_proof_photo',
                ...$_FILES['address_proof_photo']
            ]
        );

        $uploadFilesRes = array();

        try {
            // upload files to media store then store their url in the database
            foreach($files as $file) {
                $fileData = [
                    'name' => $file['doc_type'],
                    'type' => $file['type'],
                ];

                $newFileName = generateUniqueFileName($fileData);
                $uploadResult = $store->uploadApi()->upload($file['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/drivers'
                ]);

                $uploadFilesRes[$file['doc_type']] = [
                    'doc_type' => $file['doc_type'],
                    'url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id']
                ];
            }

            $appPublicId = "DRV-APP-" . strtoupper(generateNanoId(10));

            // prepare application details to be stored in the database
            $appDetails = [
                'user_id' => $userAccId,
                'driver_app_public_id' => $appPublicId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'ext_name' => $extName,
                'alt_email' => $altEmail,
                'contact_number' => $contactNumber,
                'address' => $address,
                'birthdate' => $birthDate,
                'gender' => $gender,
                'license_number' => $licenseNumber,
                'license_expiry' => $licenseExpiry,
                'agreed_to_terms' => $agreedToTerms,
                'license_img_url' => $uploadFilesRes['license_photo']['url'],
                'proof_of_address_img_url' => $uploadFilesRes['address_proof_photo']['url'],
                'nbi_clearance_img_url' => $uploadFilesRes['nbi_clearance']['url'],
                'id_picture_img_url' => $uploadFilesRes['id_picture']['url'],
            ];

            $res = createDriverApplication($conn, $appDetails);

            if($res){
                redirectUser("../../views/driver/onboarding/onboarding-success.php");
            } else {
                redirectUser("../../views/driver/onboarding/onboarding-error.php");
            }
        } catch(Exception $e) {
            if(!empty($uploadFilesRes)) {
                foreach($uploadFilesRes as $file){
                    try {
                        $store->uploadApi()->destroy($file['public_id']);
                    } catch(Exception $ex) {
                        echo "Error deleting file: " . $ex->getMessage();
                    }
                }
            }
            redirectUser("../../views/driver/onboarding/onboarding-error.php");
        }
    }
}


?>