<?php 

function addNewInvoice($conn, $invoiceData) {
    $stmt = $conn->prepare("
        INSERT INTO invoices (
            public_invoice_id,
            service_type,
            service_id,
            invoice_status,
            invoice_total_amount,
            invoice_amount_paid
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssisdd",
        $invoiceData['public_invoice_id'],
        $invoiceData['service_type'],
        $invoiceData['service_id'],
        $invoiceData['invoice_status'],
        $invoiceData['invoice_total_amount'],
        $invoiceData['invoice_amount_paid']
    );
    return $stmt->execute();
}

function addPaymentToInvoice($conn, $invoicePaymentData) {
    $stmt = $conn->prepare("
        INSERT INTO invoice_payments (
            invoice_id,
            payment_type,
            payment_id,
            amount_paid
        ) VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "isid",
        $invoicePaymentData['invoice_id'],
        $invoicePaymentData['payment_type'],
        $invoicePaymentData['payment_id'],
        $invoicePaymentData['amount_paid']
    );
    return $stmt->execute();
}


function getInvoiceById($conn, $invoiceId) {
    $stmt = $conn->prepare("
        SELECT * FROM invoices 
        WHERE invoice_id = ?
    ");
    $stmt->bind_param("i", $invoiceId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getInvoiceByServiceIdAndType($conn, $serviceId, $serviceType) {
    $stmt = $conn->prepare("
        SELECT * FROM invoices as i
        JOIN invoice_payments as ip 
        ON i.invoice_id = ip.invoice_id
        WHERE i.service_id = ? 
        AND i.service_type = ?
    ");
    $stmt->bind_param("is", $serviceId, $serviceType);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>