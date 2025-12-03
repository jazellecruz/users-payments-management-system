<?php

function addNewTransaction($conn, $transactionData) {
    $stmt = $conn->prepare("
        INSERT INTO transactions (
            public_transaction_id,
            external_transaction_id,
            paid_by,
            paid_to,
            transaction_method,
            transaction_channel,
            source_account_id,
            source_account_type,
            destination_account_id,
            destination_account_type,
            total_amount_paid,
            transaction_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssiissisisds",
        $transactionData['public_transaction_id'],
        $transactionData['external_transaction_id'],
        $transactionData['paid_by'],
        $transactionData['paid_to'],
        $transactionData['transaction_method'],
        $transactionData['transaction_channel'],
        $transactionData['source_account_id'],
        $transactionData['source_account_type'],
        $transactionData['destination_account_id'],
        $transactionData['destination_account_type'],
        $transactionData['total_amount_paid'],
        $transactionData['transaction_status']
    );
    return $stmt->execute();
}

function getTransactionWithExternalId($conn, $externalTransactionId) {
    $stmt = $conn->prepare("
        SELECT * FROM transactions 
        WHERE external_transaction_id = ?
    ");
    $stmt->bind_param("s", $externalTransactionId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateTransactionStatus($conn, $transactionId, $newStatus) {
    $stmt = $conn->prepare("
        UPDATE transactions 
        SET transaction_status = ? 
        WHERE transaction_id = ?
    ");
    $stmt->bind_param("si", $newStatus, $transactionId);
    return $stmt->execute();
}

function setProviderRefIdOfTxn($conn, $transactionId, $providerTransRefId) {
    $stmt = $conn->prepare("
        UPDATE transactions 
        SET provider_transaction_ref_id = ? 
        WHERE transaction_id = ?
    ");
    $stmt->bind_param("si", $providerTransRefId, $transactionId);
    return $stmt->execute();
}

function getTransactionById($conn, $transactionId) {
    $stmt = $conn->prepare("
        SELECT * FROM transactions 
        WHERE transaction_id = ?
    ");
    $stmt->bind_param("i", $transactionId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateChannelOfTransaction($conn, $transactionId, $newChannel) {
    $stmt = $conn->prepare("
        UPDATE transactions 
        SET transaction_channel = ? 
        WHERE transaction_id = ?
    ");
    $stmt->bind_param("si", $newChannel, $transactionId);
    return $stmt->execute();
}

?>