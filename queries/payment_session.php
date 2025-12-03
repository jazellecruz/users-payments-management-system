<?php 

function addNewPaymentSession($conn, $paymentSessionData) {
    $stmt = $conn->prepare("
        INSERT INTO payment_sessions (
            public_payment_session_id,
            payment_id,
            transaction_id,
            service_type,
            service_id,
            payment_session_status
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "siisis",
        $paymentSessionData['public_session_id'],
        $paymentSessionData['payment_id'],
        $paymentSessionData['transaction_id'],
        $paymentSessionData['service_type'],
        $paymentSessionData['service_id'],
        $paymentSessionData['payment_session_status']
    );
    return $stmt->execute();
}

function getPaymentSessionByPaymentId($conn, $paymentId) {
    $stmt = $conn->prepare("
        SELECT * FROM payment_sessions 
        WHERE payment_id = ?
    ");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updatePaymentSessionStatus($conn, $paymentSessionId, $newStatus) {
    $stmt = $conn->prepare("
        UPDATE payment_sessions 
        SET payment_session_status = ? 
        WHERE payment_session_id = ?
    ");
    $stmt->bind_param("si", $newStatus, $paymentSessionId);
    return $stmt->execute();
}

function getPaymentSessionWithServiceId($conn, $serviceId, $serviceType) {
    $stmt = $conn->prepare("
        SELECT * FROM payment_sessions 
        WHERE service_id = ? AND service_type = ?
    ");
    $stmt->bind_param("is", $serviceId, $serviceType);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getPaymentSessionById($conn, $paymentSessionId) {
    $stmt = $conn->prepare("
        SELECT * FROM payment_sessions 
        WHERE payment_session_id = ?
    ");
    $stmt->bind_param("i", $paymentSessionId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getPaymentSessionOfPaymentType($conn, $serviceId, $paymentType) {
    $stmt = $conn->prepare("
        SELECT ps.* FROM payment_sessions ps
        JOIN payments p ON ps.payment_id = p.payment_id
        WHERE ps.service_id = ? AND p.payment_type = ?
    ");
    $stmt->bind_param("is", $serviceId, $paymentType);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

?>