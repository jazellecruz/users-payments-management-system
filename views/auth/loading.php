<?php
require_once __DIR__ . '/../../utils/utils.php';
require_once __DIR__ . '/../../db/db_conn.php';
require_once __DIR__ . '/../../utils/auth.php';

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
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if ($email === '' || $password === '') {
        echo "⚠️ Missing email or password.";
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, password_hash, acc_img_url, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param('ss', $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        
        if (verifyPassword($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['acc_img_url'] = $row['acc_img_url'];


            if ($row['role'] === 'bus_rep') {
                redirectUser('../business_rep/dashboard/applications.php');
                exit();
            } 

            if ($row['role'] === 'driver') {
                $driverStmt = $conn->prepare("SELECT driver_id FROM drivers WHERE user_id = ?");
                $driverStmt->bind_param('i', $row['user_id']);
                $driverStmt->execute();
                $driverResult = $driverStmt->get_result();

                if ($driverRow = $driverResult->fetch_assoc()) {
                    $_SESSION['driverId'] = $driverRow['driver_id'];
                    // redirect driver to their driver dashboard
                    echo "Redirecting to driver dashboard...";
                    exit();
                } else {
                    // redirect driver to a different dashboard if unverified
                    redirectUser('../driver/dashboard/applications.php');
                    exit();
                }
            }

            if ($row['role'] === 'admin') {
                redirectUser('../admin/dashboard/index.php');
                exit();
            }

            if ($row['role'] === 'basic') {
                echo "Login Successful. Redirecting to journeolink homepage...";
                exit();
            }
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
