<?php 

function generateSession($user, $roleProfile = null) {
    $_SESSION = [
        "userId" => $user["user_id"],
        "email" => $user["email"],
        "lastName" => $user["last_name"],
        "firstName" => $user["first_name"],
        "role" => $user["role"]
    ];

    if($user["role"] === "admin") {
        $_SESSION['adminId'] = $roleProfile['admin_id'];
    } 

    if($user["role"] === "driver") {
        $_SESSION['driverId'] = $roleProfile['driver_id'];
    }

    if($user["role"] === "bus_rep") {
        $_SESSION['busRepId'] = $roleProfile['business_rep_id'];
    }
}

function destroySession() {
    session_destroy();
    $_SESSION = [];
}

function redirectUser($redirectUrl) {
    header('Location: ' . $redirectUrl);
    exit;
}

?>