<?php 

require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../db/db_conn.php';

require_once __DIR__ . '/../queries/invoices.php';
require_once __DIR__ . '/../queries/ride_booking.php';
require_once __DIR__ . '/../queries/payment_session.php';
require_once __DIR__ . '/../queries/payments.php';

require_once __DIR__ . '/../enums/Prefix.php';
require_once __DIR__ . '/../enums/ServiceType.php';
require_once __DIR__ . '/../enums/TransactionStatus.php';

function generateInvoiceForRideBooking($serviceId){
    $conn = getDbConnection();

    try {
        $bookingDetails = getRideBookingById($conn, $serviceId);
        $bookingId = $bookingDetails['booking_id'];

        $paymentSession = getPaymentSessionWithServiceId(
            $conn,
            $serviceId,
            ServiceType::RIDE_BOOKING->value
        );

        $paymentId = $paymentSession['payment_id'];
        $payment = getPaymentById($conn, $paymentId);
        
        if($paymentSession['payment_session_status'] !== TransactionStatus::PAID->value){
            // throw error - cannot generate invoice for incomplete payment session
        }

        $publicInvoiceId = Prefix::INVOICE->value . "-" . strtoupper(generateNanoId(12));

        $invoiceData = [
            'public_invoice_id' => $publicInvoiceId,
            'service_type' => ServiceType::RIDE_BOOKING->value,
            'service_id' => $bookingId,
            'invoice_status' => TransactionStatus::PAID->value,
            'invoice_total_amount' => $payment['total_amount'],
            'invoice_amount_paid' => $payment['total_amount']
        ];

        $isInvoiceCreated = addNewInvoice($conn, $invoiceData);

        if(!$isInvoiceCreated){
            // throw error - invoice creation failed
        }
        
        $invoiceId = $conn->insert_id;

        $invoicePaymentData = [
            'invoice_id' => $invoiceId,
            'payment_type' => $payment['payment_type'],
            'payment_id' => $paymentId,
            'amount_paid' => $payment['total_amount']
        ];

        $isPaymentAddedToInvoice = addPaymentToInvoice($conn, $invoicePaymentData);

        if(!$isPaymentAddedToInvoice){
            // throw error - invoice creation failed
        }

        $invoice = getInvoiceById($conn, $invoiceId);

        return [
            'public_invoice_id' => $invoice['public_invoice_id'],
            'invoice_status' => $invoice['invoice_status'],
            'service_type' => $invoice['service_type'],
            'service_id' => $invoice['service_id'],
            'invoice_total_amount' => $invoice['invoice_total_amount'],
            'invoice_amount_paid' => $invoice['invoice_amount_paid'],
            'created_at' => $invoice['created_at']
        ];
    } catch(Exception $e) {
        throw $e;
    }
    
}

// TO DO: refactor, do not pass the db conn to the lower level service functions
function generateInvoiceForRentalPayment($conn, $paymentSessId) {
    try { 
        $paymentSession = getPaymentSessionById($conn, $paymentSessId);
        $payment = getPaymentById($conn, $paymentSession['payment_id']);
        $transaction = getTransactionById($conn, $paymentSession['payment_id']);

        $serviceType = ServiceType::CAR_RENTAL->value;
        $serviceId = $paymentSession['service_id']; 
        $service = getCarRentalById($conn, $serviceId);

        if($paymentSession['payment_session_status'] !== TransactionStatus::PAID->value){
            // throw error - cannot generate invoice for incomplete payment session
        }

        $publicInvoiceId = Prefix::INVOICE->value . "-" . strtoupper(generateNanoId(12));

        $invoiceData = [
            'public_invoice_id' => $publicInvoiceId,
            'service_type' => ServiceType::CAR_RENTAL->value,
            'service_id' => $serviceId,
            'invoice_status' => TransactionStatus::PAID->value,
            'invoice_total_amount' => $payment['total_amount'],
            'invoice_amount_paid' => $payment['total_amount']
        ];

        $isInvoiceCreated = addNewInvoice($conn, $invoiceData);

        if(!$isInvoiceCreated){
            // throw error - invoice creation failed
        }
        
        $invoiceId = $conn->insert_id;

        $invoicePaymentData = [
            'invoice_id' => $invoiceId,
            'payment_type' => $payment['payment_type'],
            'payment_id' => $payment['payment_id'],
            'amount_paid' => $payment['total_amount']
        ];

        $isPaymentAddedToInvoice = addPaymentToInvoice($conn, $invoicePaymentData);
        $invoiceId = $conn->insert_id;
        $invoice = getInvoiceById($conn, $invoiceId);
        return [
            'public_invoice_id' => $invoice['public_invoice_id'],
            'invoice_status' => $invoice['invoice_status'],
            'service_type' => $invoice['service_type'],
            'service_id' => $invoice['service_id'],
            'invoice_total_amount' => $invoice['invoice_total_amount'],
            'invoice_amount_paid' => $invoice['invoice_amount_paid'],
            'created_at' => $invoice['created_at']
        ];
    } catch(Exception $e) {
        throw $e;
    }
}

?>