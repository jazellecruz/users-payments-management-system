<?php
require_once __DIR__ . '/../../db/db_conn.php';

$conn = getDBConnection();

if (isset($_POST['signup'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'basic';

    $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role)
            VALUES ('$first_name', '$last_name', '$email', '$password', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "Signup successful!";
        
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password_hash'])) {
            echo "Login successful! Welcome, " . $row['first_name'];
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Email not found.";
    }

} else {
    echo "Invalid action.";
}
?>
