<?php
require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../db/db_conn.php';

session_start();
$MAX_RETRIES = 5;
$attempts = 0;
$conn = null;

while ($attempts < $MAX_RETRIES) {
    $conn = getDBConnection();
    if ($conn && !$conn->connect_error) {
        break;
    }
    $attempts++;
    sleep($delay);
    $delay *= 2; 
}

if (!$conn || $conn->connect_error) {
    die("❌ Failed to connect to database after $MAX_RETRIES attempts.");
}

if (isset($_POST['signup'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
    $email      = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $role       = 'basic';

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        echo "⚠️ Missing required fields.";
        exit;
    }

    $hashed = hashPassword($password);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $first_name, $last_name, $email, $hashed, $role);

    if ($stmt->execute()) {
        echo "✅ Signup successful!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();

} else if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($email === '' || $password === '') {
        echo "⚠️ Missing email or password.";
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, first_name, email, password_hash, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param('ss', $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (verifyPassword($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            echo "✅ Login successful! Welcome, " . htmlspecialchars($row['first_name']) . " (" . $row['role'] . ")";
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ Email or role not found.";
    }

    $stmt->close();

} else {
    echo "⚠️ Invalid action.";
}

$conn->close();
?>
