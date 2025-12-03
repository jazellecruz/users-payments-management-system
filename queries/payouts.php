<?php 

function addNewPayout($conn, $payoutData) {
    $sql = "INSERT INTO payouts (
            public_payout_id, 
            service_type,
            service_id,
            paid_to,
            total_payout_amount,
            transaction_id,
            payment_id,
            source_account_id,
            destination_account_id,
            payout_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssiidiiiis",
        $payoutData['public_payout_id'],
        $payoutData['service_type'],
        $payoutData['service_id'],
        $payoutData['paid_to'],
        $payoutData['total_payout_amount'],
        $payoutData['transaction_id'],
        $payoutData['payment_id'],
        $payoutData['source_account_id'],
        $payoutData['destination_account_id'],
        $payoutData['payout_status']
    );

    return $stmt->execute();
}

function getPayoutById($conn, $payoutId) {
    $stmt = $conn->prepare("SELECT * FROM payouts WHERE payout_id = ?");
    $stmt->bind_param("i", $payoutId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

?>