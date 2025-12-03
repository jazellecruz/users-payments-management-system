<?php 

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/db_conn.php';
require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../ex/CustomException.php';

require_once __DIR__ . '/../enums/TransactionStatus.php';
require_once __DIR__ . '/../enums/TransactionSourceType.php';
require_once __DIR__ . '/../enums/TransactionType.php';
require_once __DIR__ . '/../enums/ServiceType.php';
require_once __DIR__ . '/../enums/PaymentType.php';
require_once __DIR__ . '/../enums/PaymentChannel.php';
require_once __DIR__ . '/../enums/PaymentMethod.php';
require_once __DIR__ . '/../enums/UserType.php';
require_once __DIR__ . '/../enums/Prefix.php';
require_once __DIR__ . '/../enums/ExceptionTypes.php';
require_once __DIR__ . '/../enums/XenditTransactionStatus.php';
require_once __DIR__ . '/../enums/EnvironmentMode.php';
require_once __DIR__ . '/../enums/LedgerEntryType.php';
require_once __DIR__ . '/../enums/Currency.php';
require_once __DIR__ . '/../enums/SourceType.php';

require_once __DIR__ . '/../queries/payments.php';
require_once __DIR__ . '/../queries/platform_accounts.php';
require_once __DIR__ . '/../queries/transactions.php';
require_once __DIR__ . '/../queries/payment_session.php';
require_once __DIR__ . '/../queries/ride_booking.php';
require_once __DIR__ . '/../queries/accounts.php';
require_once __DIR__ . '/../queries/payouts.php';
require_once __DIR__ . '/../queries/driver_earnings.php';

require_once __DIR__ . '/../config/internal_api.php';
require_once __DIR__ . '/../config/external_api.php'; 
require_once __DIR__ . '/../config/config.php'; 

require_once __DIR__ . '/ledger_service.php';
require_once __DIR__ . '/invoices_service.php';

use \GuzzleHttp\Client;

// sample response from xendit transfer API (DO NOT DELETE/UNCOMMENT):
// {
//     "transfer_id": "f3f360d6-536b-44ec-a7fd-0cf796bfe7a1",
//     "source_user_id": "68c3f7135d83025f14eda7fa",
//     "destination_user_id": "68c7d128f44eec072079083d",
//     "amount": 2000,
//     "currency": "PHP",
//     "reference": "transfer-926632b7-d4e2-4de6-9f5d-26fb1d386988",
//     "status": "SUCCESSFUL",
//     "created": "2025-11-19T17:36:24.8373931Z"
// }
function transferToXenditAccount($transferDetails) {
    $amount = $transferDetails['amount'];
    $destinationAcc = $transferDetails['destination_user_id'];
    $sourceAccount = $transferDetails['source_user_id'];
    $reference = $transferDetails['reference'];
    $referenceCurrency = 'PHP';

    $httpClient = generateXenditHttpClient();

    $payload = [
        'amount' => $amount,
        'currency' => $referenceCurrency,
        'destination_user_id' => $destinationAcc,
        'source_user_id' => $sourceAccount,
        'reference' => $reference
    ];

    $jsonPayload = json_encode($payload);

    $res = $httpClient->post(XENDIT_TRANSFERS_ENDPOINT, [
        'body' => $jsonPayload
    ]);

    $resBody = $res->getBody();
    $decodedRes = json_decode($resBody, true);

    return $decodedRes;
}

function processPayoutForRentalPayment($conn,$paymentSessId) {
    try {
        $paymentSession = getPaymentSessionById($conn, $paymentSessId);
        $paymentSessionId = $paymentSession['payment_session_id'];
        $payment = getPaymentById($conn, $paymentSession['payment_id']);
        $paymentId = $payment['payment_id'];
        $transaction = getTransactionById($conn, $paymentSession['transaction_id']);
        $transactionId = $transaction['transaction_id'];

        $serviceId = $paymentSession['service_id'];
        $service = getCarRentalById($conn, $serviceId);
        $serviceType = ServiceType::CAR_RENTAL->value;
        $serviceProvider = getUserAccById($conn, $service['driver_id']);
        $serviceProviderId = $serviceProvider['user_id'];  
        $publicServiceId = $service['public_rental_id'];

        $platformAccount = getMasterAccount($conn, XENDIT_MASTER_ACCOUNT_NUM);
        $platformAccountId = $platformAccount['platform_account_id'];
        $platformAccountExtAccId = $platformAccount['external_account_id'];
        $serviceProviderAcc = getPlatformSubAccountByUserId(
            $conn, 
            $serviceProviderId, 
            UserType::DRIVER->value
        );
        $serviceProviderAccId = $serviceProviderAcc['platform_account_id'];
        $serviceProviderExtAccId = $serviceProviderAcc['external_account_id'];

        $publicPayoutId = Prefix::PAYOUT->value . "-" . strtoupper(generateNanoId(12));
        $publicTxnId = Prefix::TRANSACTION->value . "-" . strtoupper(generateNanoId(12));
        $extTxnId = Prefix::EXTERNAL_TRANSACTION->value . "-" . strtoupper(generateNanoId(12));

        $taxCollected = convertStrToFloat($payment['tax_pay']);
        $platformFee = convertStrToFloat($payment['platform_fee']);
        $netEarnings = convertStrToFloat($payment['net_pay']);
        $totalRevenue = $taxCollected + $platformFee;
        
        $amtToAddToServiceProviderAcc = $netEarnings;

        $payoutDetails =  [
            'public_payout_id' => $publicPayoutId,
            'service_type' => $serviceType,
            'service_id' => $serviceId,
            'paid_to' => $serviceProviderId,
            'total_payout_amount' => $netEarnings,
            'payment_id' => $paymentId,
            'source_account_id' => $platformAccountId,
            'destination_account_id' => $serviceProviderAccId,
            'payout_status' => TransactionStatus::PAID->value
        ];

        $txnDetails = [
            'public_transaction_id' => $publicTxnId,
            'external_transaction_id' => $extTxnId, 
            'paid_by' => null, 
            'paid_to' => $serviceProviderId, 
            'transaction_method' => PaymentMethod::XENDIT_INTERNAL_TRANSFER->value,
            'transaction_channel' => PaymentChannel::XENDIT_INTERNAL_TRANSFER->value,
            'source_account_id' => $platformAccountId, 
            'source_account_type' => SourceType::PLATFORM_ACCOUNT->value,
            'destination_account_id' => $serviceProviderAccId, 
            'destination_account_type' => TransactionSourceType::PLATFORM_ACCOUNT->value,
            'total_amount_paid' => $netEarnings,
            'transaction_status' => TransactionStatus::PAID->value,
        ];

        $ledgerEntryDetails = [
            'entry_type' => LedgerEntryType::DEBIT->value,
            'transaction_type' => TransactionType::PAYOUT->value,
            'entry_category' => EntryCategory::PAYOUT_AMOUNT->value,
            'payment_type' => null,
            'payment_id' => null,
            'withdrawal_id' => null,
            'amount' => $netEarnings,
            'notes' => 'Payout for car rental ID ' . $publicServiceId
        ];

        $transferPayload = [
            'amount' => $netEarnings,
            'currency' => Currency::PHP->value,
            'destination_user_id' => $serviceProviderExtAccId,
            'source_user_id' => $platformAccountExtAccId,
            'reference' => $extTxnId
        ];

        $driverEarnData = [
            "user_id" => $serviceProviderId,
            "payment_id" => $paymentId, 
            "service_type" => $serviceType,
            "service_id" => $serviceId,
            "amount_earned" => $netEarnings
        ];

        $isTransferSuccessful = false;

        $transferRes = transferToXenditAccount($transferPayload);
        $transferResStatus = $transferRes['status'];

        if($transferResStatus === XenditTransactionStatus::FAILED->value) {
            $isTransferSuccessful = false;
            throw new CustomException(
                null,
                'Failed to transfer payout funds to driver for payment session id ' . $paymentSessId,
                ExceptionTypes::INTERNAL_TRANSFER_FAILED,
                ExceptionTypes::INTERNAL_TRANSFER_FAILED->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_TRANSFER_FAILED->toErrorData()['message']
            );
        }

        $isTransferSuccessful = true;
        $providerRefId = $transferRes['transfer_id'];

        $isTxnInserted = addNewTransaction($conn, $txnDetails);

        if(!$isTxnInserted) {
            throw new CustomException(
                null,
                'Failed to create payout transaction record for payment session id ' . $paymentSessId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
        $newTxnId = $conn->insert_id;

        $newTxn = getTransactionById($conn, $newTxnId);
        $payoutDetails['transaction_id'] = $newTxnId;

        $isPayoutInserted = addNewPayout($conn, $payoutDetails);

        if(!$isPayoutInserted) {
            throw new CustomException(
                null,
                'Failed to create payout record for payment session id ' . $paymentSessId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
        
        $newPayoutId = $conn->insert_id;

        setProviderRefIdOfTxn(
            $conn, 
            $newTxnId, 
            $providerRefId
        );

        $ledgerEntryDetails['transaction_id'] = $payoutDetails['transaction_id'];
        $ledgerEntryDetails['payout_id'] = $newPayoutId;

        $isLedgerEntryCreated = createLedgerEntry($conn, $ledgerEntryDetails);

        if(!$isLedgerEntryCreated) {
            throw new CustomException(
                null,
                'Failed to create ledger entry for payout transaction id ' . $newTxnId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $isDriverEarningInserted = addNewDriverEarningRecord($conn, $driverEarnData);

        if(!$isDriverEarningInserted) {
            throw new CustomException(
                null,
                'Failed to create driver earning record for payout transaction id ' . $newTxnId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        // update sub account balance for driver
        $isServiceProviderAccUpdated = addToAccountBalance(
            $conn,
            $serviceProviderAcc['platform_account_id'],
            $amtToAddToServiceProviderAcc
        );
        
        if(!$isServiceProviderAccUpdated) {
            throw new CustomException(
                null,
                'Failed to update platform sub account balance for payout transaction id ' . $newTxnId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
        return [
            'payout_id' => $newPayoutId,
            'associated_txn_id' => $newTxnId,
            'associated_payment_id' => $payment['payment_id'],
            'associated_payment_sess_id' => $paymentSessId
        ];
    } catch(Exception $e) {
        throw $e;
        if($e instanceof CustomException) {
            throw $e;
        } else {
            throw new CustomException(
                $e,
                'Runtime exception occurred during payout processing for payment session id ' . $paymentSessId,
                ExceptionTypes::RUNTIME_EXCEPTION,
                ExceptionTypes::RUNTIME_EXCEPTION->toErrorData()['statusCode'],
                ExceptionTypes::RUNTIME_EXCEPTION->toErrorData()['message']
            );
        }
    }
}

?>