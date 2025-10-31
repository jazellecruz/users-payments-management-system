<?php 

require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/business.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../utils/utils.php';

$conn = getDbConnection();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['action'] === 'cancel_application') {
        $applicationId = $_POST['application_id'];
        $status = 'cancelled';

        $res = updateBusinessApplicationStatus($conn, $applicationId, $status);

        // unsafe redirection :')
        $redirectUrl = $_SERVER["HTTP_REFERER"];

        if($res) {
            redirectUser($redirectUrl);
            exit();
        } else {
            echo "FAILED TO CANCEL APPLICATION";
            exit();
        }

    }   

    if($_POST['action'] === 'add_application') {
        $conn = getDbConnection();
        $mediaStore = getMediaStore();
        
        $uploadedPhotos = [];

        try {
            $businessRepId = sanitizeData($conn, $_POST['business_rep_id']);
            $businessRoleId = sanitizeData($conn, $_POST['bus_rep_role']);

            $businessName = sanitizeData($conn, $_POST['bus_name']);
            $businessDesc = sanitizeData($conn, $_POST['bus_desc']);
            $businessTypeId = sanitizeData($conn, $_POST['bus_type']);
            $businessContactNum = sanitizeData($conn, $_POST['business_contact_num']);
            $businessEmail = sanitizeData($conn, $_POST['business_email']);
            $isBusinessOperating = $_POST['operating_cb'] ? 1 : 0;
            $agreedToTerms = !empty($_POST['terms_conds_checkbox']) ?? false;
            $businessPermit = $_FILES['business_permit'];
            $authLetter = is_uploaded_file($_FILES['auth_letter']['tmp_name']) ? $_FILES['auth_letter'] : null;

            // address of business 
            $busUnitNum = sanitizeData($conn, $_POST['bus_unit_number']);
            $busStreet = sanitizeData($conn, $_POST['bus_street']);
            $busPostalCode = sanitizeData($conn, $_POST['bus_postal_code']);
            $busCity = sanitizeData($conn, $_POST['bus_city']);
            $busProvince = sanitizeData($conn, $_POST['bus_province']);
            $busCountry = sanitizeData($conn, $_POST['bus_country']);
            $long = sanitizeData($conn, $_POST['bus_longitude']);
            $lat = sanitizeData($conn, $_POST['bus_latitude']);

            $businessFiles = [
                'business_permit' => [
                    'doc_type' => 'business_permit',
                    ...$businessPermit
                ],
            ];

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

            // upload first business files and photos to media store
            $uploadedBusinessFiles = [];
            $uploadedBusinessPhotos = [];

            foreach($businessFiles as $file) {
                $publicId = $file['doc_type'] . '-' . generateNanoId(18);
                
                $uploadResult = $mediaStore->uploadApi()->upload($file['tmp_name'], [
                    'public_id' => $publicId,
                    'folder' => 'journeolink/businesses'
                ]);

                $uploadedBusinessFiles[$file['doc_type']] = [
                    'url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id']
                ];
            }

            foreach($businessPhotos as $photo) {
                $publicId = 'business-photo-' . generateNanoId(18);

                $uploadResult = $mediaStore->uploadApi()->upload($photo['tmp_name'], [
                    'public_id' => $publicId,
                    'folder' => 'journeolink/businesses'
                ]);

                $uploadedBusinessPhotos[] = [
                    'url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id']
                ];
            }

            $conn->begin_transaction();
            $conn->autocommit(FALSE);

            $publicAppId = "BUS-APP-" . strtoupper(generateNanoId(10));

            // prepare data to store in db
            $applicationData = [
                'public_business_application_id' => $publicAppId,
                'business_rep_id' => $businessRepId,
                'business_rep_position_id' => $businessRoleId,
                'business_name' => $businessName,
                'business_desc' => $businessDesc,
                'business_type_id' => $businessTypeId,
                'business_contact_num' => $businessContactNum,
                'business_email' => $businessEmail,
                'business_unit_number' => $busUnitNum,
                'business_street' => $busStreet,
                'business_postal_code' => $busPostalCode,
                'business_city' => $busCity,
                'business_province' => $busProvince,
                'business_country' => $busCountry,
                'loc_lat' => $lat,
                'loc_long' => $long,
                'business_permit_url' => $uploadedBusinessFiles['business_permit']['url'],
                'authorization_letter_url' => isset($uploadedBusinessFiles['auth_letter']) ? $uploadedBusinessFiles['auth_letter']['url'] : null,
                'agreed_to_terms' => $agreedToTerms,
                'is_operating' => $isBusinessOperating,
            ];

            $newApp = createBusinessApplication($conn, $applicationData);

            if(!$newApp) {
                throw new Exception("Failed to create business application.");
            }

            $newAppId = $conn->insert_id;

            foreach($uploadedBusinessPhotos as $photo) {
                $photoData = [
                    'business_app_id' => $newAppId,
                    'photo_url' => $photo['url'],
                    'public_id' => $photo['public_id']
                ];

                $newPhoto = createBusinessAppPhotos($conn, $photoData);

                if(!$newPhoto) {
                    throw new Exception("Failed to add business application photo.");
                }
            }

            $conn->commit();

            header('Content-Type: application/json');
            http_response_code(200);
            $response = [
                'status' => 'success',
                'userMsg' => 'Business application submitted successfully.'
            ];
            echo json_encode($response);
        } catch(Exception $e) {
            $conn->rollback();

            if(!empty($uploadedBusinessFiles)) {
                $publicIds = array_column($uploadedBusinessFiles, 'public_id');
                $mediaStore->adminApi()->deleteAssets($publicIds);    
            }

            if(!empty($uploadedBusinessPhotos)) {
                $publicIds = array_column($uploadedBusinessPhotos, 'public_id');
                $mediaStore->adminApi()->deleteAssets($publicIds);    
            }

            header('Content-Type: application/json');
            http_response_code(500);
            $response = [
                'status' => 'error',
                'userMsg' => 'Failed to submit business application. Please try again later.',
                'error' => $e->getMessage()
            ];
            echo json_encode($response);
        } finally {
            $conn->close();
        }
        
    }  
}

?>