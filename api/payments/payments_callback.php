<?php 

require_once __DIR__ . '/../../db/db_conn.php';

require_once __DIR__ . '/../../queries/transactions.php';
require_once __DIR__ . '/../../queries/payments.php';
require_once __DIR__ . '/../../queries/payment_session.php';
require_once __DIR__ . '/../../queries/ride_booking.php';
require_once __DIR__ . '/../../queries/platform_revenue.php';

require_once __DIR__ . '/../../enums/XenditTransactionStatus.php';
require_once __DIR__ . '/../../enums/TransactionStatus.php';
require_once __DIR__ . '/../../enums/TransactionType.php';
require_once __DIR__ . '/../../enums/LedgerEntryType.php';  
require_once __DIR__ . '/../../enums/PaymentType.php';  
require_once __DIR__ . '/../../enums/ServiceType.php';  
require_once __DIR__ . '/../../enums/EntryCategory.php';
require_once __DIR__ . '/../../enums/XenditPaymentChannel.php';

require_once __DIR__ . '/../../services/ledger_service.php';
require_once __DIR__ . '/../../services/payouts_service.php';
require_once __DIR__ . '/../../services/invoices_service.php';
require_once __DIR__ . '/../../services/platform_accounts_service.php';

require_once __DIR__ . '/../../ex/CustomException.php';
require_once __DIR__ . '/../../enums/ExceptionTypes.php';

/**
 * FINANCIAL EVENTS EXPECTED TO HAPPEN:
 * - If transaction is successful: update Transaction and Payment status to 'collected'
 * - Return 200 HTTP response to Xendit to acknowledge receipt of callback (IMPORTANT)
 */

// SAMPLE JSON RESPONSE FROM XENDIT FOR REFERENCE (DO NOT DELETE/UNCOMMENT):
// {
//     "id": "691953f726d6bc37164d69ff",
//     "external_id": "EX-TXN3W7FDZZAVGB1",
//     "user_id": "68c3f7135d83025f14eda7fa",
//     "payment_method": "EWALLET",
//     "status": "PAID",
//     "merchant_name": "Journeolink",
//     "amount": 134.4,
//     "paid_amount": 134.4,
//     "paid_at": "2025-11-16T04:33:08.257Z",
//     "ewallet_type": "GCASH",
//     "is_high": false,
//     "success_redirect_url": "https://subpectoral-unpunctually-inez.ngrok-free.dev/users-payments-management-system/success.php?service_type=ride_booking&booking_id=RIDE-BKG-SCFPHFOFS1M6",
//     "failure_redirect_url": "https://subpectoral-unpunctually-inez.ngrok-free.dev/users-payments-management-system/failed.php?service_type=ride_booking&booking_id=RIDE-BKG-SCFPHFOFS1M6",
//     "created": "2025-11-16T04:32:56.245Z",
//     "updated": "2025-11-16T04:33:11.013Z",
//     "currency": "PHP",
//     "payment_channel": "GCASH",
//     "payment_id": "ewc_5709c1af-c0e6-447e-a670-631ddc94345d",
//     "payment_method_id": "pm-b143185c-921b-478c-a993-f417088e2123"
// }

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resPayload = file_get_contents('php://input');
    $decodedRes = json_decode($resPayload, true);

    // log the payload to a file for debugging
    file_put_contents("../../callback_log.txt", "[". (new DateTime("now", new DateTimeZone("Asia/Manila")))->format('Y-m-d H:i:s') . "]" . $resPayload . PHP_EOL, FILE_APPEND);

    if($decodedRes === null) {
        // invalid json payload
        http_response_code(400);
        exit;
    }

    $dbConn = getDbConnection();

    $voidTransaction = getTransactionWithExternalId($dbConn, $decodedRes['external_id']);

    if(empty($voidTransaction)) {
        // send 200 response to avoid repeated callbacks for non-existent transactions
        http_response_code(200);
        exit;
    }


    // TO DO REFACTOR: breakdown into smaller functions, too much business logic
    // in the callback handler
    if($decodedRes['status'] == XenditTransactionStatus::PAID->value) {
        $conn = getDbConnection();

        $extTransId = $decodedRes['external_id'];

        try {
            // fetch transaction by external transaction id
            $transaction = getTransactionWithExternalId($conn, $extTransId);
            $transactionId = $transaction['transaction_id'];
            $payment = getPaymentByTransactionId($conn, $transactionId);
            $paymentId = $payment['payment_id'];
            $paymentSession = getPaymentSessionByPaymentId($conn, $paymentId);
            $paymentSessionId = $paymentSession['payment_session_id'];
            $serviceType = $paymentSession['service_type'];
            $serviceId = $paymentSession['service_id'];

            if(
                $transaction['transaction_status'] == TransactionStatus::COLLECTED->value ||
                $payment['payment_status'] == TransactionStatus::COLLECTED->value || 
                $paymentSession['payment_session_status'] == TransactionStatus::COLLECTED->value
            ) {
                throw new CustomException(
                    null,
                    'Payment already collected for transaction with external id ' . $extTransId,
                    ExceptionTypes::PAYMENT_ALREADY_COLLECTED,
                    ExceptionTypes::PAYMENT_ALREADY_COLLECTED->toErrorData()['statusCode'], 
                    ExceptionTypes::PAYMENT_ALREADY_COLLECTED->toErrorData()['message']
                );
            }

            $conn->begin_transaction();
            $conn->autocommit(false);

            if($serviceType == ServiceType::RIDE_BOOKING->value) {
                $newTxnStatus = TransactionStatus::COLLECTED->value;
            }

            if($serviceType == ServiceType::CAR_RENTAL->value) {
                $newTxnStatus = TransactionStatus::PAID->value;
            }

            $providerRefUpdRes = setProviderRefIdOfTxn($conn, $transactionId, $decodedRes['payment_id']);
            $updatedTransStat = updateTransactionStatus($conn, $transactionId, $newTxnStatus);
            $updatedPaymentStatus = updatePaymentStatus($conn, $paymentId, $newTxnStatus);
            $updatedPaySessStat = updatePaymentSessionStatus($conn, $paymentSessionId, $newTxnStatus);

            $decodedPaymentChannel = $decodedRes['payment_channel'];
            $chosenPaymentChannel = null;

            if($chosenPaymentChannel == PaymentMethod::CASH->value){
                $chosenPaymentChannel = PaymentMethod::CASH->value;
            } else {
                $chosenPaymentChannel = XenditPaymentChannel::toLocalPaymentChannelEnum($decodedRes['payment_channel'])->value;
            }

            updateChannelOfPayment($conn, $payment['payment_id'], $chosenPaymentChannel);
            updateChannelOfTransaction($conn, $transaction['transaction_id'], $chosenPaymentChannel);

            if(!$providerRefUpdRes || 
                !$updatedTransStat || 
                !$updatedPaymentStatus || 
                !$updatedPaySessStat
            ) {
                throw new CustomException(
                    null,
                    'Failed to process payment callback for transaction with external id ' . $extTransId,
                    ExceptionTypes::INTERNAL_SERVER_ERROR,
                    ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                    ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
                );    
            }

            if($payment['payment_type'] == PaymentType::DOWN_PAYMENT->value ||
               $payment['payment_type'] == PaymentType::BALANCE_PAYMENT->value
            ) {

                $carRentalService = getCarRentalById($conn, $serviceId);
                $publicRentalId = $carRentalService['public_rental_id'];

                $paymentTypeReadable = strtolower(PaymentType::fromString($payment['payment_type'])->toReadableString());

                $ledgerEntryForPayment = array(
                    [
                        'entry_type' => LedgerEntryType::CREDIT->value,
                        'transaction_id' => $transaction['transaction_id'],
                        'transaction_type' => TransactionType::PAYMENT->value,
                        'entry_category' => EntryCategory::NET_PAY->value,
                        'payment_type' => $payment['payment_type'],
                        'payment_id' => $payment['payment_id'], 
                        'withdrawal_id' => null,
                        'payout_id' => null,
                        'amount' => $payment['net_pay'],
                        'notes' => 'Net pay from ' . $paymentTypeReadable . ' for car rental ' . $publicRentalId
                    ],
                    [
                        'entry_type' =>  LedgerEntryType::CREDIT->value,
                        'transaction_id' => $transaction['transaction_id'],
                        'transaction_type' => TransactionType::PAYMENT->value,
                        'entry_category' => EntryCategory::TAX_PAY->value,
                        'payment_type' => $payment['payment_type'],
                        'payment_id' => $payment['payment_id'],
                        'withdrawal_id' => null,
                        'payout_id' => null,
                        'amount' => $payment['tax_pay'],
                        'notes' => 'Tax pay from ' . $paymentTypeReadable . ' for car rental ' . $publicRentalId
                    ],
                    [
                        'entry_type' =>  LedgerEntryType::CREDIT->value,
                        'transaction_id' => $transaction['transaction_id'],
                        'transaction_type' => TransactionType::PAYMENT->value,
                        'entry_category' => EntryCategory::PLATFORM_FEE->value,
                        'payment_type' => $payment['payment_type'],
                        'payment_id' => $payment['payment_id'],
                        'withdrawal_id' => null,
                        'payout_id' => null,
                        'amount' => $payment['platform_fee'],
                        'notes' => 'Platform fee from ' . $paymentTypeReadable . ' for car rental ' . $publicRentalId
                    ],
                );

                $isLedgerInserted = createLedgerEntries($conn, $ledgerEntryForPayment);

                if(!$isLedgerInserted) {
                    throw new CustomException(
                        null,
                        'Failed to record payment to ledger.',
                        ExceptionTypes::INTERNAL_SERVER_ERROR,
                        ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                        ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
                    );    
                }

                $netEarnings = convertStrToFloat($payment['net_pay']);
                $platformFee = convertStrToFloat($payment['platform_fee']);
                $taxCollected = convertStrToFloat($payment['tax_pay']);
                $totalRevenue = $platformFee + $taxCollected;
                
                $platformRevenue = [
                    'transaction_id' => $transactionId,
                    'payment_id' => $paymentId,
                    'service_type' => $serviceType,
                    'service_id' => $serviceId,
                    'platform_fee' => $platformFee,
                    'tax_amount' => $taxCollected,
                    'total_revenue' => $totalRevenue
                ];

                $isPlatformRevenueRecorded = addNewPlatformRevenueRecord(
                    $conn,
                    $platformRevenue
                );

                // process payout handles the ledger entry for payout internally
                $payoutRes = processPayoutForRentalPayment(
                    $conn, 
                    $paymentSession['payment_session_id']
                );

                // TO DO REFACTOR: idk if this is still needed,
                // since when error occurs, an exception is already thrown
                if(empty($payoutRes)) {
                    throw new CustomException(
                        null,
                        'Failed to process payout for rental payment with payment session id ' . $paymentSession['payment_session_id'],
                        ExceptionTypes::INTERNAL_SERVER_ERROR,
                        ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                        ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
                    );    
                }

                $associatedPaymentSessId = $payoutRes['associated_payment_sess_id'];
                $invoiceRes = generateInvoiceForRentalPayment(
                    $conn,
                    $associatedPaymentSessId
                );
            }

            $conn->commit();
            http_response_code(200);
            echo "Success";
        } catch(Exception $e) {
            echo $e;
            $conn->rollback();

            if($e instanceof CustomException) {
                $errType = $e->getExceptionType()->value;
                $errLogDesc = $e->getErrorDesc();
                $errStack = null;
            } else {
                $errType = ExceptionTypes::RUNTIME_EXCEPTION->value;
                $errLogDesc = $e->getMessage();
                $errStack = $e->getTraceAsString();
            }

            file_put_contents(
                "../../error_log.txt",
                "[" . (new DateTime("now", new DateTimeZone("Asia/Manila")))->format('Y-m-d H:i:s') . "] " . 
                "[" . $errType . "] " .
                "[" . $errLogDesc . "] " . 
                PHP_EOL,
                FILE_APPEND
            );

            http_response_code(500);
        }
    }
}

?>