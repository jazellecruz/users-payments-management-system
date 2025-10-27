<?php 

require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../queries/business.php';

$conn = getDbConnection();
$mediaStore = getMediaStore();

// to do: seperate address of business in a table
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // only for onboarding (both business rep and business profile application)
    if(isset($_POST['action']) && $_POST['action'] === 'business_onboarding') {
       
        $userAccId = sanitizeData($conn, $_POST['user_acc_id']);
        $firstName = sanitizeData($conn, $_POST['first_name']);
        $lastName = sanitizeData($conn, $_POST['last_name']); 
        $middleName = sanitizeData($conn, $_POST['middle_name']);
        $extName = sanitizeData($conn, $_POST['ext_name']) ?? null;
        $birthDate = sanitizeData($conn, $_POST['birthdate']);
        $gender = sanitizeData($conn, $_POST['gender']);
        $address = sanitizeData($conn, $_POST['address']);
        $contactNum = sanitizeData($conn, $_POST['contact_number']);
        $altEmail = sanitizeData($conn, $_POST['alt_email']);
        $businessRoleId = sanitizeData($conn, $_POST['bus_rep_role']);
        $authLetter = is_uploaded_file($_FILES['auth_letter']['tmp_name']) ? $_FILES['auth_letter'] : null;

        // Business Profile Details
        $businessName = sanitizeData($conn, $_POST['bus_name']);
        $businessDesc = sanitizeData($conn, $_POST['bus_desc']);
        $businessTypeId = sanitizeData($conn, $_POST['bus_type']);
        $businessContactNum = sanitizeData($conn, $_POST['business_contact_num']);
        $businessEmail = sanitizeData($conn, $_POST['business_email']);
        $isBusinessOperating = $_POST['operating_cb'] ? 1 : 0;
        $agreedToTerms = !empty($_POST['terms_conds_checkbox']) ?? false;

        // address of business 
        $busUnitNum = sanitizeData($conn, $_POST['bus_unit_number']);
        $busStreet = sanitizeData($conn, $_POST['bus_street']);
        $busPostalCode = sanitizeData($conn, $_POST['bus_postal_code']);
        $busCity = sanitizeData($conn, $_POST['bus_city']);
        $busProvince = sanitizeData($conn, $_POST['bus_province']);
        $busCountry = sanitizeData($conn, $_POST['bus_country']);
        $long = sanitizeData($conn, $_POST['bus_longitude']);
        $lat = sanitizeData($conn, $_POST['bus_latitude']);


        // business files
        $businessFiles = array(
            'business_permit' => [
                'doc_type' => 'business_permit',
                ...$_FILES['business_permit']
            ],
            'valid_id' => [
                'doc_type' => 'valid_id',
                ...$_FILES['valid_id']
            ],
        );

        if(!is_null($authLetter)) {
            $businessFiles['auth_letter'] = [
                'doc_type' => 'auth_letter',
                ...$authLetter
            ];
        }

        $businessPhotos = [];

        // populate business photos array
        for($i = 0; $i < count($_FILES['business_photos']['name']); $i++) {
            $businessPhotos[] = [
                'doc_type' => 'business_photo',
                'name' => $_FILES['business_photos']['name'][$i],
                'type' => $_FILES['business_photos']['type'][$i],
                'tmp_name' => $_FILES['business_photos']['tmp_name'][$i],
                'error' => $_FILES['business_photos']['error'][$i],
                'size' => $_FILES['business_photos']['size'][$i]
            ];
        }

        $uploadedBusinessFiles = [];
        $uploadedBusinessPhotos = [];
        
        try {
            // upload all files to media store and store references
            foreach($businessFiles as $file) {
                $fileData = [
                    'name' => $file['doc_type'],
                    'type' => $file['type'],
                ];

                $newFileName = generateUniqueFileName($fileData);
                $uploadResult = $mediaStore->uploadApi()->upload($file['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/businesses'
                ]);

                $uploadedBusinessFiles[$file['doc_type']] = [
                    'doc_type' => $file['doc_type'],
                    'url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id']
                ];
            }

            foreach($businessPhotos as $file) {
                $fileData = [
                    'name' => $file['doc_type'],
                    'type' => $file['type'],
                ];

                $newFileName = generateUniqueFileName($fileData);
                $uploadResult = $mediaStore->uploadApi()->upload($file['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/businesses'
                ]);


                $uploadedBusinessPhotos[] = [
                    'doc_type' => $file['doc_type'],
                    'url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id']
                ];

            }

            $publicRepId = "BUS-REP-" . strtoupper(generateNanoId(10));

            // prepare business rep details
            $businessRep = [
                'user_id' => $userAccId,
                'public_id' => $publicRepId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'ext_name' => $extName,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'user_address' => $address,
                'active_phone_num' => $contactNum,
                'alternative_email' => $altEmail,
                'valid_id_url' => $uploadedBusinessFiles['valid_id']['url'],
            ];

            $publicAppId = "BUS-APP-" . strtoupper(generateNanoId(10));

            $businessDetails = [
                'public_business_application_id' => $publicAppId ,
                'business_name' => $businessName,
                'business_desc' => $businessDesc,
                'business_type_id' => $businessTypeId,
                'business_contact_num' => $businessContactNum,
                'business_email' => $businessEmail,
                'is_operating' => $isBusinessOperating,
                'agreed_to_terms' => $agreedToTerms, 
                'business_unit_number' => $busUnitNum,
                'business_street' => $busStreet,
                'business_postal_code' => $busPostalCode,
                'business_city' => $busCity,
                'business_province' => $busProvince,
                'business_country' => $busCountry,
                'loc_long' => $long,
                'loc_lat' => $lat,
                'business_permit_url' => $uploadedBusinessFiles['business_permit']['url'],
                'authorization_letter_url' => isset($uploadedBusinessFiles['auth_letter']) ? $uploadedBusinessFiles['auth_letter']['url'] : null,
            ];

        
            $conn->autocommit(false);
            $conn->begin_transaction();

            createBusinessRep($conn, $businessRep);
            
            $businessRepId = $conn->insert_id;
            $businessDetails['business_rep_id'] = $businessRepId;
            $businessDetails['business_rep_position_id'] = $businessRoleId;
            
            createBusinessApplication($conn, $businessDetails);
            $businessAppId = $conn->insert_id;

            // save business photos
            foreach ($uploadedBusinessPhotos as $photo) {
                $photoToUpload = [];
                $photoToUpload['business_app_id'] = $businessAppId;
                $photoToUpload['photo_url'] = $photo['url'];
                $photoToUpload['public_id'] = $photo['public_id'];
                createBusinessAppPhotos($conn, $photoToUpload);
            }

            // commit transaction
            $conn->commit();

            redirectUser('../../views/business_rep/onboarding/onboarding-success.php');
        } catch(Exception $e) {
             $conn->rollback();
            print_r($e->getMessage());
            exit;
           
            try {
                // delete uploaded files if transaction fails
                if(!empty($uploadedBusinessPhotos)) {
                    foreach($uploadedBusinessPhotos as $file){
                        $mediaStore->uploadApi()->destroy($file['public_id']);
                    }
                }

                if(!empty($uploadedBusinessFiles)) {
                    foreach($uploadedBusinessFiles as $file){
                        $mediaStore->uploadApi()->destroy($file['public_id']);
                    }
                }
            } catch(Exception $ex) {
                // log error
                error_log("Error deleting uploaded files: " . $ex->getMessage());
            }
            // redirect to error page
            redirectUser('../../views/business_rep/onboarding/onboarding-error.php');
            exit;
        }

        $conn->close();
    }

}

?>