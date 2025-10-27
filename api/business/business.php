<?php   

require_once __DIR__ . '../../../db/db_conn.php';
require_once __DIR__ . '../../../utils/utils.php';
require_once __DIR__ . '../../../utils/auth.php';
require_once __DIR__ . '../../../queries/business.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'edit_business_profile_img') { 
        $mediaStore = getMediaStore();
        $conn = getDbConnection();

        $newProfileImage = is_uploaded_file($_FILES['business_profile_image']['tmp_name']) ? $_FILES['business_profile_image'] : null;
        $businessId = sanitizeData($conn, $_POST['business_id']);
        $newFileName = 'BUS-PROFILE-' . strtoupper(generateNanoId(10));
        $uploadedImg = null;

        try{
            $uploadedImg = $mediaStore->uploadApi()->upload($newProfileImage['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/businesses'
            ]);

            $result = updateBusinessProfileImg($conn, $businessId, $uploadedImg['secure_url']);

            if($result) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }
            
        } catch(Exception $e) {
            $mediaStore->uploadApi()->destroy($newFileName);
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'edit_business_cover_img') { 
        $mediaStore = getMediaStore();
        $conn = getDbConnection();

        $newCoverImage = is_uploaded_file($_FILES['business_cover_image']['tmp_name']) ? $_FILES['business_cover_image'] : null;
        $businessId = sanitizeData($conn, $_POST['business_id']);
        $newFileName = 'BUS-COVER-' . strtoupper(generateNanoId(10));
        $uploadedImg = null;

        try{
            $uploadedImg = $mediaStore->uploadApi()->upload($newCoverImage['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/businesses'
            ]);

            $result = updateBusinessCoverImg($conn, $businessId, $uploadedImg['secure_url']);

            if($result) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }
            
        } catch(Exception $e) {
            $mediaStore->uploadApi()->destroy($newFileName);
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'edit_business_info') {
        foreach($_POST as $key => $value) {
            echo $key . ': ' . $value . '<br>';
        }
        $conn = getDbConnection();

        $public_business_id = sanitizeData($conn, $_POST['public_business_id']);
        $businessName = sanitizeData($conn, $_POST['business_name']);
        $businessName = sanitizeData($conn, $_POST['business_name']);
        $businessType = sanitizeData($conn, $_POST['business_type']);
        $businessDesc = sanitizeData($conn, $_POST['business_desc']);
        $businessUnitNumber = sanitizeData($conn, $_POST['business_unit_number']);
        $businessStreet = sanitizeData($conn, $_POST['business_street']);
        $businessPostalCode = sanitizeData($conn, $_POST['business_postal_code']);
        $businessCity = "Quezon City"; // fixed data 
        $businessProvince = "Metro Manila"; // fixed data 
        $businessCountry = "Philippines"; // fixed data 
        $locLat = sanitizeData($conn, $_POST['loc_lat']);
        $locLong = sanitizeData($conn, $_POST['loc_long']);
        $businessContactNum = sanitizeData($conn, $_POST['business_contact_num']);
        $businessEmail = sanitizeData($conn, $_POST['business_email']);
        $isOperating = $_POST['is_operating'] === 'true' ? true : false;

        $businessId = getBusinessIdByPublicId($conn, $public_business_id); 
       
        if(!$businessId) {
            echo "Error: Business not found.";
            exit;
        }

        $businessData = [
            'business_id'         => $businessId,
            'business_name'        => $businessName,
            'business_type_id'        => $businessType,
            'business_desc'        => $businessDesc,
            'business_unit_number' => $businessUnitNumber,
            'business_street'      => $businessStreet,
            'business_postal_code' => $businessPostalCode,
            'business_city'        => $businessCity,     // fixed data
            'business_province'    => $businessProvince, // fixed data
            'business_country'     => $businessCountry,  // fixed data
            'loc_lat'              => $locLat,
            'loc_long'             => $locLong,
            'business_contact_num' => $businessContactNum,
            'business_email'       => $businessEmail,
            'is_operating'         => $isOperating
        ];

        try {
            $result = updateBusinessInfo($conn, $businessData);

            if($result) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'delete_business_imgs') {
        $mediaStore = getMediaStore();
        $conn = getDbConnection();
        
        $photosToDelete = $_POST['photosToDelete'];
        $businessId = sanitizeData($conn, $_POST['business_id']);

        try {
            $conn->autocommit(FALSE);
            $conn->begin_transaction();

            $retrievedPhotos = null;
            $photoIds = null;
            $photosPublicIds = null;

            if(count($photosToDelete) >= count(getBusinessPhotosByBusinessId($conn, $businessId))) {
                echo("You cannot delete all business photos. At least one photo must remain.");
                exit;
            }

            if(count($photosToDelete) > 1) {
                $photoIds = $photosToDelete;
                $retrievedPhotos = getBusinessPhotosById($conn, $photoIds);
                $res = deleteBusinessPhotosById($conn, $photoIds);

                if(!$res) {
                    echo("Error deleting photos.");
                    exit;
                }

                $photosPublicIds = array_column($retrievedPhotos, 'public_id');
                // // foreach($retrievedPhotos as $photo) {
                // //     $photosPublicIds[] = $photo['public_id'];
                // // }

                $conn->commit();
                $mediaStore->adminApi()->deleteAssets($photosPublicIds);
            }

            if(count($photosToDelete) === 1) {
                $photoId = $photosToDelete[0];
                $retrievedPhoto = getBusinessPhotoById($conn, $photoId);

                if(!$retrievedPhoto) {
                    echo("Photo not found.");
                    exit;
                }

                $res = deleteBusinessPhotosById($conn, $photoId);

                if(!$res) {
                    echo("Error deleting photos.");
                    exit;
                }

                $conn->commit();
                $mediaStore->uploadApi()->destroy($retrievedPhoto['public_id']);
            }

            $redirectUrl = $_SERVER['HTTP_REFERER'];
            redirectUser($redirectUrl);

        } catch(Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'add_business_imgs') {
        $conn = getDbConnection();
        $mediaStore = getMediaStore();
        $newImgs = $_FILES['business_images'];
        $businessId = sanitizeData($conn, $_POST['business_id']);
        $uploadedImgs = [];

        try {
            $conn->autocommit(FALSE);
            $conn->begin_transaction();

            for($i = 0; $i < count($newImgs['name']); $i++) {
                $tmpName = $newImgs['tmp_name'][$i];
                $newFileName = 'business-photo-' . strtoupper(generateNanoId(10));
                $uploadedImg = $mediaStore->uploadApi()->upload($tmpName, [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/businesses'
                ]);

                $uploadedImgs[] = [
                    'business_id' => $businessId,
                    'photo_url' => $uploadedImg['secure_url'],
                    'public_id' => $uploadedImg['public_id']
                ];
            }


            foreach($uploadedImgs as $imgData) {
                createBusinessPhotos($conn, $imgData);
            }

            $conn->commit();
            $redirectUrl = $_SERVER['HTTP_REFERER'];
            redirectUser($redirectUrl);
        } catch(Exception $e) {
            $conn->rollback();
            $imgPublicIds = array_column($uploadedImgs, 'public_id');
            $mediaStore->adminApi()->deleteAssets($imgPublicIds);
            echo "Error: " . $e->getMessage();
        }
    }
    
}   

?>