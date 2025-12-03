<?php

function addNewDriverEarningRecord($conn, $earningData) {
    $stmt = $conn->prepare("
        INSERT INTO driver_earnings (
            user_id,
            payment_id,
            service_type,
            service_id,
            amount_earned
        ) VALUES (?,?,?,?,?)
    ");
    $stmt->bind_param(
        "iisid",
        $earningData['user_id'],
        $earningData['payment_id'],
        $earningData['service_type'],
        $earningData['service_id'],
        $earningData['amount_earned'],
    );
    return $stmt->execute();
}

?>