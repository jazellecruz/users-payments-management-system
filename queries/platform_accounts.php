<?php 

function addNewPlatformAccount($conn, $accData) {
    $stmt = $conn->prepare("INSERT INTO platform_accounts 
    (public_platform_account_id, 
    platform_account_name, 
    platform_account_email,
    platform_account_type,
    external_account_id,
    owner_type,
    owner_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssi", 
        $accData['public_platform_account_id'], 
        $accData['platform_account_name'], 
        $accData['platform_account_email'],
        $accData['platform_account_type'],
        $accData['external_account_id'],
        $accData['owner_type'],
        $accData['owner_id']
    );
    return $stmt->execute();
}

function getPlatformAccountByPublicId($conn, $publicPlatformAccountId) {
    $stmt = $conn->prepare("SELECT * FROM platform_accounts WHERE public_platform_account_id = ?");
    $stmt->bind_param("s", $publicPlatformAccountId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getMasterAccount($conn, $externalAccountId) {
    $stmt = $conn->prepare("SELECT * FROM platform_accounts 
        WHERE external_account_id = ? 
        AND owner_type = 'platform';");
    $stmt->bind_param("s", $externalAccountId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getPlatformSubAccountByUserId($conn, $ownerId, $ownerType) {
    $stmt = $conn->prepare("SELECT * FROM platform_accounts 
        WHERE platform_account_type = 'sub_account'
        AND owner_type = ?
        AND owner_id = ?;");
    $stmt->bind_param("si", $ownerType, $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAccBalanceByPlatformAcctId($conn, $platformAccountId) {
    $stmt = $conn->prepare("
        SELECT * FROM platform_accounts_balances
        WHERE platform_account_id = ?;
    ");
    $stmt->bind_param("i", $platformAccountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = $result->fetch_assoc();
    return $res;
}

function addNewPendingDeduction($conn, $deductionData) {
    $stmt = $conn->prepare("
        INSERT INTO pending_account_deductions
        (platform_account_id, user_id, deduction_for, amount_to_deduct, deduction_status)
        VALUES (?, ?, ?, ?, ?);
    ");
    $stmt->bind_param(
        "iisds",
        $deductionData['platform_account_id'],
        $deductionData['user_id'],
        $deductionData['deduction_for'],
        $deductionData['amount_to_deduct'],
        $deductionData['deduction_status']
    );
    return $stmt->execute();
}

function updatePlatformAccountBalance($conn, $platformAccountId, $newBalance) {
    $stmt = $conn->prepare("
        UPDATE platform_accounts_balances
        SET current_balance = ?
        WHERE platform_account_id = ?;
    ");
    $stmt->bind_param("di", $newBalance, $platformAccountId);
    return $stmt->execute();
}
?>