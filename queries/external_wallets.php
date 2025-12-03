<?php

function addNewEwallet($conn, $ewalletData) {
    $stmt = $conn->prepare("
    INSERT INTO external_wallets (
        public_external_wallet_id, 
        owner_type, 
        owner_id,
        external_wallet_provider, 
        external_wallet_number, 
        external_wallet_name
    ) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssisss", 
        $ewalletData['public_external_wallet_id'], 
        $ewalletData['owner_type'],
        $ewalletData['owner_id'],
        $ewalletData['external_wallet_provider'],
        $ewalletData['external_wallet_number'],
        $ewalletData['external_wallet_name']
    );
    return $stmt->execute();
}

function getExternalWalletById($conn, $ewalletId) {
    $stmt = $conn->prepare("SELECT * FROM external_wallets WHERE external_wallet_id = ?");
    $stmt->bind_param("i", $ewalletId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

?>