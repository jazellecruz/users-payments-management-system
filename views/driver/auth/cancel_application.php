<?php
require_once '../../../db/db_conn.php'; 

$conn = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = $_POST['id'];
  $stmt = $conn->prepare("UPDATE driver_applications SET status = 'cancelled' WHERE driver_app_public_id = ?");
  $stmt->bind_param("s", $id);

  if ($stmt->execute()) {
    echo "Application cancelled.";
  } else {
    echo "Failed to cancel application.";
  }

  $stmt->close();
  $conn->close();
}
?>
