<?php 

function addNewPayment($conn, $paymentIntentData) {
    $stmt = $conn->prepare("
    INSERT INTO payments (
        public_payment_id,
        service_type,
        service_id,
        payment_type,
        payment_method,
        payment_channel,
        paid_by,
        total_amount,
        platform_fee,
        net_pay,
        tax_pay,
        transaction_id,
        payment_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssisssiddddis", 
        $paymentIntentData['public_payment_id'],
        $paymentIntentData['service_type'],
        $paymentIntentData['service_id'],
        $paymentIntentData['payment_type'],
        $paymentIntentData['payment_method'],
        $paymentIntentData['payment_channel'],
        $paymentIntentData['paid_by'],
        $paymentIntentData['total_amount'],
        $paymentIntentData['platform_fee'],
        $paymentIntentData['net_pay'],
        $paymentIntentData['tax_pay'],
        $paymentIntentData['transaction_id'],
        $paymentIntentData['payment_status']
    );
    return $stmt->execute();
}

function setTransactionIdForPayment($conn, $paymentId, $transactionId) {
    $stmt = $conn->prepare("
    UPDATE payments 
    SET transaction_id = ? 
    WHERE payment_id = ?");
    $stmt->bind_param("ii", $transactionId, $paymentId);
    return $stmt->execute();
}

function getPaymentById($conn, $paymentId) {
    $stmt = $conn->prepare("
    SELECT * FROM payments 
    WHERE payment_id = ?");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updatePaymentStatus($conn, $paymentId, $newStatus) {
    $stmt = $conn->prepare("
    UPDATE payments 
    SET payment_status = ? 
    WHERE payment_id = ?");
    $stmt->bind_param("si", $newStatus, $paymentId);
    return $stmt->execute();
}

function getPaymentByTransactionId($conn, $transactionId) {
    $stmt = $conn->prepare("
    SELECT * FROM payments 
    WHERE transaction_id = ?");
    $stmt->bind_param("i", $transactionId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getPaymentByServiceId($conn, $serviceType, $serviceId) {
    $stmt = $conn->prepare("
    SELECT * FROM payments 
    WHERE service_type = ? AND service_id = ?");
    $stmt->bind_param("si", $serviceType, $serviceId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateChannelOfPayment($conn, $paymentId, $newChannel) {
    $stmt = $conn->prepare("
    UPDATE payments 
    SET payment_channel = ? 
    WHERE payment_id = ?");
    $stmt->bind_param("si", $newChannel, $paymentId);
    return $stmt->execute();
}