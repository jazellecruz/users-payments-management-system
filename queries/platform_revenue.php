<?php 

function addNewPlatformRevenueRecord($conn, $revenueData) {
    $stmt = $conn->prepare("
        INSERT INTO platform_revenue (
            transaction_id,
            payment_id,
            service_type,
            service_id,
            platform_fee,
            tax_collected,
            total_revenue
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iisiddd",
        $revenueData['transaction_id'],
        $revenueData['payment_id'],
        $revenueData['service_type'],
        $revenueData['service_id'],
        $revenueData['platform_fee'],
        $revenueData['tax_pay'],
        $revenueData['total_revenue']
    );
    return $stmt->execute();
}


?>