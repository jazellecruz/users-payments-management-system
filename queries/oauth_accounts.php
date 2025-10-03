<?php 

function getOauthAccountWithOpenId($conn, $openId) {
    $stmt = $conn->prepare("SELECT * FROM oauth_accounts WHERE oauth_user_id = ?");
    $stmt->bind_param("s", $openId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
};

function createOauthAccount($conn, $oauthAcc) {
    $stmt = $conn->prepare("INSERT INTO oauth_accounts (user_id, oauth_user_id, provider, access_token, refresh_token, token_expiry) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isssss", 
        $oauthAcc['userId'], 
        $oauthAcc['oauthUserId'], 
        $oauthAcc['provider'], 
        $oauthAcc['accessToken'], 
        $oauthAcc['refreshToken'], 
        $oauthAcc['tokenExpiry']);
    return $stmt->execute();
}

?>