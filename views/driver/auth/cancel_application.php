<?php
session_start();
require_once '../../../db/db_conn.php'; 

$conn = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $appId = $_POST['id'];
  $userId = $_SESSION['user_id'] ?? null;

  if (!$userId) {
    http_response_code(401);
    echo "Unauthorized: Please log in.";
    exit;
  }

  $check = $conn->prepare("SELECT user_id FROM driver_applications WHERE driver_app_public_id = ?");
  $check->bind_param("s", $appId);
  $check->execute();
  $check->bind_result($ownerId);
  $check->fetch();
  $check->close();

  if ($ownerId !== $userId) {
    http_response_code(403);
    echo "Forbidden: You can only cancel your own application.";
    exit;
  }

  $stmt = $conn->prepare("UPDATE driver_applications SET status = 'cancelled' WHERE driver_app_public_id = ?");
  $stmt->bind_param("s", $appId);

  if ($stmt->execute()) {
    echo "Application cancelled.";
  } else {
    echo "Failed to cancel application.";
  }

  $stmt->close();
  $conn->close();
}
?>
