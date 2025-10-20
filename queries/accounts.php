<?php 

function createUserAccount($conn, $user) {
    $email = $user['email'];
    $password = $user['password'];
    $role = $user['role'];
    $firstName = $user['firstName'];
    $lastName = $user['lastName'];

    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $password, $firstName, $lastName, $role);
    return $stmt->execute();
}

function getUserAccById($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
};

function getUserAccByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getUserByEmailAndRole($conn, $email, $role) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateUserProfileImg($conn, $userId, $newImgUrl) {
    $stmt = $conn->prepare("UPDATE users SET acc_img_url = ? WHERE user_id = ?");
    $stmt->bind_param("si", $newImgUrl, $userId);
    return $stmt->execute();
}

function updateUserInfo($conn, $userId, $data) {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $data['first_name'], $data['last_name'], $userId);
    return $stmt->execute();
}

?>