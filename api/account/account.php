<?php  

session_start();

require_once __DIR__ . '../../../db/db_conn.php';
require_once __DIR__ . '../../../utils/utils.php';
require_once __DIR__ . '../../../utils/auth.php';
require_once __DIR__ . '../../../queries/accounts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action']) && $_POST['action'] === 'edit_user_image') { 
        $mediaStore = getMediaStore();
        $conn = getDbConnection();

        $newProfileImage = is_uploaded_file($_FILES['profile_image']['tmp_name']) ? $_FILES['profile_image'] : null;
        $userId = sanitizeData($conn, $_POST['user_id']);

        try{
            $newFileName = 'USR-IMG-' . strtoupper(generateNanoId(10));

            $uploadedImg = $mediaStore->uploadApi()->upload($newProfileImage['tmp_name'], [
                    'public_id' => $newFileName,
                    'folder' => 'journeolink/users'
            ]);

            $result = updateUserProfileImg($conn, $userId, $uploadedImg['secure_url']);

            if($result) {
                $_SESSION['acc_img_url'] = $uploadedImg['secure_url'];

                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }

            
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if(isset($_POST['action']) && $_POST['action'] === 'edit_account_info') {
        $conn = getDbConnection();

        $userId = sanitizeData($conn, $_POST['user_id']);
        $firstName = sanitizeData($conn, $_POST['first_name']);
        $lastName = sanitizeData($conn, $_POST['last_name']);

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];

        try {
            $result = updateUserInfo($conn, $userId, $data);

            if($result) {
                $_SESSION['first_name'] = $data['first_name'];
                $_SESSION['last_name'] = $data['last_name'];

                $redirectUrl = $_SERVER['HTTP_REFERER'];
                redirectUser($redirectUrl);
            }
        } catch(Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>