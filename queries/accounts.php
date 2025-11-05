<?php 

function createUserAccount($conn, $user) {
    $email = $user['email'];
    $password = $user['password'];
    $role = $user['role'];
    $firstName = $user['firstName'];
    $lastName = $user['lastName'];
    $accImgUrl = isset($user['acc_img_url']) ? $user['acc_img_url'] : null;

    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, first_name, last_name, role, acc_img_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $email, $password, $firstName, $lastName, $role, $accImgUrl);
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

function updateAccountPassword($conn, $userId, $newHashedPassword) {
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->bind_param("si", $newHashedPassword, $userId);
    return $stmt->execute();
}

function createUnverifiedUserAccount($conn, $user) {
    $email = $user['email'];
    $verificationCode = $user['verification_code'];
    $hashedPassword = $user['hashedPassword'];
    $role = $user['role'];
    $firstName = $user['firstName'];
    $lastName = $user['lastName'];
    $expiresAt = $user['expires_at'];

    $stmt = $conn->prepare("INSERT INTO unverified_users (email, password_hash, verification_code, role, first_name, last_name, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $email, $hashedPassword, $verificationCode, $role, $firstName, $lastName, $expiresAt);
    return $stmt->execute();
}

function getUnverifiedUserByCodeAndEmail($conn, $email, $verificationCode) {
    $stmt = $conn->prepare("SELECT * FROM unverified_users WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function deleteUnverifiedUserById($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM unverified_users WHERE unverified_user_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

?>