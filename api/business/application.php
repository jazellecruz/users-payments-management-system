<?php 

require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../queries/business.php';
require_once __DIR__ . '/../../utils/auth.php';

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
}

?>