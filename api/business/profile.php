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

    if(isset($_POST['action']) && $_POST['action'] === 'create_bus_rep_profile') {
        $conn = getDbConnection();
        $mediaStore = getMediaStore();

        $userId = sanitizeData($conn, $_POST['user_id']);
        $firstName = sanitizeData($conn, $_POST['first_name']);
        $lastName = sanitizeData($conn, $_POST['last_name']);
        $middleName = sanitizeData($conn, $_POST['middle_name']);
        $extName = sanitizeData($conn, $_POST['ext_name']);
        $birthDate = sanitizeData($conn, $_POST['birth_date']);
        $gender = sanitizeData($conn, $_POST['gender']);
        $address = sanitizeData($conn, $_POST['address']);
        $contactNum = sanitizeData($conn, $_POST['contact_num']);
        $altEmail = sanitizeData($conn, $_POST['alt_email']);
        $validId = $_FILES['valid_id'];
        $idPic = $_FILES['id_picture'];


        try {
            $existingRep = getBusinessRepByUserId($conn, $userId);

            if($existingRep) {
                // to do: create custom exception class to 
                // attach custom messages and error codes to user facing errors
                throw new Exception('Business Representative profile already exists.');
            }

            $conn->autocommit(FALSE);
            $conn->begin_transaction();

            $uploadedFiles = [];

            // save files first on media store
            $validIdFileName = 'valid_id-' . strtoupper(generateNanoId(10));
            $idPicFileName = 'id_pic-' . strtoupper(generateNanoId(10));

            $publicBusRepId = 'BUS-REP-' . strtoupper(generateNanoId(10));

            $filesToUpload = [
                'valid_id' => [
                    'docType' => 'valid_id',
                    'file' => $validId,
                    'public_id' => $validIdFileName
                ],
                'id_picture' => [
                    'docType' => 'id_picture',
                    'file' => $idPic,
                    'public_id' => $idPicFileName
                ]
            ];

            foreach($filesToUpload as $file) {
                $res = $mediaStore->uploadApi()->upload($file['file']['tmp_name'], [
                    'public_id' => $file['public_id'],
                    'folder' => 'journeolink/business_representatives'
                ]);

                $uploadedFiles[$file['docType']] = [
                    'url' => $res['secure_url'],
                    'public_id' => $res['public_id']
                ];
            }

            // prepare data for insertion
            $repData = [
                'user_id' => $userId,
                'public_id' => $publicBusRepId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'ext_name' => $extName,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'user_address' => $address,
                'active_phone_num' => $contactNum,
                'alternative_email' => $altEmail,
                'valid_id_url' => $uploadedFiles['valid_id']['url'],
                'profile_img_url' => $uploadedFiles['id_picture']['url']
                // terms acceptance can be added later blah blah
            ];

            $res = createBusinessRep($conn, $repData);
            $conn->commit();

            header('Content-Type: application/json');
            http_response_code(200);
            $response = [
                'status' => 'success',
                'message' => 'Your Business Representative profile has been successfully created.'
            ];
            echo json_encode($response);
        }  catch(Exception $e) {
            $conn->rollback();
            
            // delete uploaded files in case of failure
            if(!empty($uploadedFiles)) {
                $filesPublicIds = array_column($uploadedFiles, 'public_id');
                $mediaStore->adminApi()->deleteAssets($filesPublicIds);
            }

            header('Content-Type: application/json');
            http_response_code(500);
            $response = [
                'status' => 'error',
                'userMsg' => 'An error occurred while creating your Business Representative profile. Please try again later.',
                'error' => $e->getMessage()
            ];
            echo json_encode($response);
        }
    }
}

?>