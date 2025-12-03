<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/db_conn.php';
require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../ex/CustomException.php';

require_once __DIR__ . '/../enums/TransactionStatus.php';
require_once __DIR__ . '/../enums/TransactionSourceType.php';
require_once __DIR__ . '/../enums/ServiceType.php';
require_once __DIR__ . '/../enums/PaymentType.php';
require_once __DIR__ . '/../enums/PaymentChannel.php';
require_once __DIR__ . '/../enums/UserType.php';
require_once __DIR__ . '/../enums/Prefix.php';
require_once __DIR__ . '/../enums/ExceptionTypes.php';
require_once __DIR__ . '/../enums/XenditTransactionStatus.php';
require_once __DIR__ . '/../enums/EnvironmentMode.php';
require_once __DIR__ . '/../enums/LedgerEntryType.php';
require_once __DIR__ . '/../enums/EntryCategory.php';
require_once __DIR__ . '/../enums/TransactionType.php';
require_once __DIR__ . '/../enums/Currency.php';

require_once __DIR__ . '/../queries/payments.php';
require_once __DIR__ . '/../queries/platform_accounts.php';
require_once __DIR__ . '/../queries/transactions.php';
require_once __DIR__ . '/../queries/payment_session.php';
require_once __DIR__ . '/../queries/ride_booking.php';
require_once __DIR__ . '/../queries/accounts.php';
require_once __DIR__ . '/../queries/payouts.php';
require_once __DIR__ . '/../queries/driver_earnings.php';
require_once __DIR__ . '/../queries/platform_revenue.php';

require_once __DIR__ . '/../config/internal_api.php';
require_once __DIR__ . '/../config/external_api.php'; 
require_once __DIR__ . '/../config/config.php'; 

require_once __DIR__ . '/ledger_service.php';
require_once __DIR__ . '/payouts_service.php';
require_once __DIR__ . '/invoices_service.php';

use \GuzzleHttp\Client;

// oh my lord andami pang kulang 🙂

// NOTE: Xendit does not allow early settlement of transactions
// in test environment, so for this reason payments will be considered paid
//  in this system after creation blah blah blah

/**
 * @param initializePaymentDetails => [
 *      "user" => [
 *          "paid_by" => [
 *              "user_id" => INT,
 *              "first_name" => STR,
 *              "last_name" => STR,
 *              "email" => STR,
 *              "phone_number" => STR
 *          ]
 *      ],
 *      "service" => [
 *          "service_type" => ENUM,
 *          "service_id" => INT,
 *          "public_service_id" => STR
 *      ],
 *     "payment" => [
 *          "payment_type" => ENUM,
 *          "payment_method" => ENUM,
 *          "payment_channel" => ENUM,
 *          "total_amount" => DECIMAL,
 *          "platform_fee" => DECIMAL,
 *          "net_pay" => DECIMAL,
 *          "tax_pay" => DECIMAL
 *      ],
 *     ]
 *  @return string|null => redirect url for checkout or success page
 */

// TO DO: Refactor parameter list too long, remove unnecessary data
// that should be fetched from db instead and is outside the scope
//  of payment initialization
function initializePayment($initialPaymentDetails) {
    $conn = getDBConnection();

    try {
        $publicPaymentId = Prefix::PAYMENT->value . "-". strtoupper(generateNanoId(12));
        $publicTransactionId = Prefix::TRANSACTION->value . "-". strtoupper(generateNanoId(12));
        $externalTransactionId = Prefix::EXTERNAL_TRANSACTION->value . "-". strtoupper(generateNanoId(12));
        $paymentSessionId = Prefix::PAYMENT_SESSION->value . "-". strtoupper(generateNanoId(12));

        $masterAccount = getMasterAccount($conn, '68c3f7135d83025f14eda7fa');

        $paymentChannel = $initialPaymentDetails['payment']['payment_method'] === PaymentMethod::CASH->value
            ? PaymentChannel::CASH->value 
            : null;

        $paymentIntentData = [
            'public_payment_id' => $publicPaymentId,
            'service_type' => $initialPaymentDetails['service']['service_type'],
            'service_id' => $initialPaymentDetails['service']['service_id'],
            'payment_type' => $initialPaymentDetails['payment']['payment_type'],
            'payment_method' => $initialPaymentDetails['payment']['payment_method'],
            'payment_channel' => $paymentChannel, // set this at callback if null 
            'paid_by' => $initialPaymentDetails['user']['paid_by'],
            'total_amount' => $initialPaymentDetails['payment']['total_amount'],
            'platform_fee' => $initialPaymentDetails['payment']['platform_fee'],
            'net_pay' => $initialPaymentDetails['payment']['net_pay'],
            'tax_pay' => $initialPaymentDetails['payment']['tax_pay'],
            'transaction_id' => null,
            'payment_status' => TransactionStatus::PENDING->value
        ];

        
        /** NOTE: paid_to will ALWAYS be null FOR booking/car rental payments 
         * since the platform is the recipient of the payment. 
         * Only time paid_to will have a value is when 
         * processing payouts to service providers/drivers
         * for completed services.
         **/
        $paidTo = null;
        
        $transactionDetails = [
            'public_transaction_id' => $publicTransactionId,
            'paid_by' => $initialPaymentDetails['user']['paid_by']['user_id'], // user making the payment (basic user)
            'paid_to' => $paidTo, 
            'transaction_method' => $initialPaymentDetails['payment']['payment_method'],
            'transaction_channel' => $paymentChannel, // set this at callback if null
            'external_transaction_id' => $externalTransactionId, // external ref id for xendit
            'source_account_id' => null, // null for booking payments (only used for internal transfers)
            'source_account_type' => null,
            'destination_account_id' => $masterAccount['platform_account_id'], // platform account (master for payments, subaccount for payouts)
            'destination_account_type' => TransactionSourceType::PLATFORM_ACCOUNT->value,
            'total_amount_paid' => $initialPaymentDetails['payment']['total_amount'],
            'transaction_status' => TransactionStatus::PENDING->value,
        ];

        $paymentSessionData = [
            'public_session_id' => $paymentSessionId,
            'service_type' => $initialPaymentDetails["service"]['service_type'],
            'service_id' => $initialPaymentDetails['service']['service_id'],
            'payment_session_status' => TransactionStatus::PENDING->value,
        ];

        $conn->begin_transaction();
        $conn->autocommit(false);

        $paymentRes = addNewPayment($conn, $paymentIntentData);
        $paymentId = $conn->insert_id;

        $transactionRes = addNewTransaction($conn, $transactionDetails); 
        $transactionId = $conn->insert_id;

        setTransactionIdForPayment($conn, $paymentId, $transactionId);

        $paymentSessionData['transaction_id'] = $transactionId;
        $paymentSessionData['payment_id'] = $paymentId;

        $paymentSessionRes = addNewPaymentSession($conn, $paymentSessionData);
        $paymentSessionId = $conn->insert_id;

        if(!$paymentRes || !$transactionRes || !$paymentSessionRes) {
            throw new CustomException(
                null,
                'Failed to create payment/transaction/session records.',
                 ExceptionTypes::INTERNAL_SERVER_ERROR,
                 ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                 "Failed to initialize payment for service. Please try again later."
            );
        }

        $queriesArr = array(
            "service_type" => $initialPaymentDetails["service"]['service_type'],
            "public_service_id" => $initialPaymentDetails["service"]['public_service_id']
        );

        $queryStr = http_build_query($queriesArr);

        // can be changed in integration based on service type
        $successRedirectUrl = XENDIT_WEBHOOK_BASE_URL . XENDIT_PAYMENT_SUCCESS_ENDPOINT . "?" . $queryStr;
        $failedRedirectUrl = XENDIT_WEBHOOK_BASE_URL . XENDIT_PAYMENT_FAILURE_ENDPOINT . "?" . $queryStr;
        
        $paymentCheckoutDetails = [
            "external_transaction_id" => $externalTransactionId,
            "amount" => $initialPaymentDetails['payment']['total_amount'],
            "failure_redirect_url" => $failedRedirectUrl,
            "success_redirect_url" => $successRedirectUrl,
            // can be, removed metadata is useless anyway since it cant be used in the callback
            "metadata" => [
                "public_payment_session_id" => $paymentSessionId,
                "service_type" => $initialPaymentDetails["service"]['service_type'],
                "service_id" => $initialPaymentDetails['service']['service_id'],
                "publc_payment_id" => $publicPaymentId
            ],
            "customer" => [
                "given_names" => $initialPaymentDetails["user"]['paid_by']["first_name"],
                "surname" => $initialPaymentDetails["user"]['paid_by']["last_name"],
                "email" => $initialPaymentDetails["user"]['paid_by']["email"],
                "mobile_number" => $initialPaymentDetails["user"]['paid_by']["phone_number"],
            ],
            
        ];

        // if payment method is cash, no need to generate checkout link
        // just return the successRedirectUrl
        $redirectUrl = $successRedirectUrl; 
        
        if($initialPaymentDetails['payment']['payment_method'] !== 'cash') {
            $paymentCheckoutUrl = generatePaymentCheckoutLink($paymentCheckoutDetails);
            $redirectUrl = $paymentCheckoutUrl;
        } 
        
        $conn->commit();

        return [
            "payment_session_id" => $paymentSessionId,
            "payment_id" => $paymentId,
            "payment_method" => $initialPaymentDetails['payment']['payment_method'],
            "total_amount_paid" => $initialPaymentDetails['payment']['total_amount'],
            "transaction_id" => $transactionId,
            "service_type" => $initialPaymentDetails["service"]['service_type'],
            "service_id" => $initialPaymentDetails['service']['service_id'],
            "redirect_url" => $redirectUrl,
            "success_redirect_url" => $successRedirectUrl
        ];
    } catch(Exception $e) {
        $conn->rollback();
        if($e instanceof CustomException) throw $e;

        $errDesc = 'Failed to initialize payment for service ' 
        . ServiceType::fromString($initialPaymentDetails['service']['public_service_id'])->value 
        . " "  
        . $initialPaymentDetails['service']['public_service_id'];

        throw new CustomException(
            $e,
            $errDesc,
            ExceptionTypes::INTERNAL_SERVER_ERROR,
            ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
            ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
        );
    }
}

/**
 * @param $sessionId INT (payment session associated with booking)
 */
function cancelBookingPayment($sessionId) {
    try {
        $conn = getDbConnection();

        $paymentSession = getPaymentSessionById($conn, $sessionId);

        if(empty($paymentSession)) {
            throw new CustomException(
                null,
                'Failed to find payment session for payment with id ' . $paymentId,
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND,
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND->toErrorData()['message']
            );
        }

        $payment = getPaymentById($conn, $paymentSession['payment_id']);
        
        if(empty($payment)) {
            throw new CustomException(
                null,
                'Failed to find payment with id ' . $id,
                ExceptionTypes::PAYMENT_NOT_FOUND,
                ExceptionTypes::PAYMENT_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::PAYMENT_NOT_FOUND->toErrorData()['message']
            );
        }

        $paymentId = $payment['payment_id'];
        $paymentSessionId = $paymentSession['payment_session_id'];
        
        $transaction = getTransactionById($conn, $paymentSession['transaction_id']);

        if(empty($transaction)) {
            throw new CustomException(
                null,
                'Failed to find transaction for payment with id ' . $paymentId,
                ExceptionTypes::TRANSACTION_NOT_FOUND,
                ExceptionTypes::TRANSACTION_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::TRANSACTION_NOT_FOUND->toErrorData()['message']
            );
        }

        $transactionId = $transaction['transaction_id'];

        $conn->begin_transaction();
        $conn->autocommit(false);

        // to do: update to void or refund in database instead of cancelled?
        // cancelling a payment that has been paid is not accurate eme
        $cancelledPaymentSession = updatePaymentSessionStatus(
            $conn, 
            $paymentSessionId, 
            TransactionStatus::CANCELLED->value
        );

        $cancelledPayment = updatePaymentStatus(
            $conn, 
            $paymentId, 
            TransactionStatus::CANCELLED->value
        );

        $cancelledTransaction = updateTransactionStatus(
            $conn, 
            $transactionId, 
            TransactionStatus::CANCELLED->value
        );

        if(
            !$cancelledPaymentSession || 
            !$cancelledPayment || 
            !$cancelledTransaction
        ) {
            throw new CustomException(
                null,
                "Failed to update payment/session/transaction to cancelled status.",
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                "Failed to cancel payment of service. Please try again later."
            );
        }
       
        if($payment['payment_method'] === PaymentMethod::E_WALLET->value) {
            $providerTransactionRefId = $transaction['provider_transaction_ref_id'];

            // call the service that refunds/voids booking payments in xendit 
            $isReversalSuccessful = reverseEwalletPayment($providerTransactionRefId);

            if(!$isReversalSuccessful) {
                throw new CustomException(
                    null,
                    "Failed to reverse ewallet payment.",
                    ExceptionTypes::INTERNAL_SERVER_ERROR,
                    ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                    "Failed to reverse ewallet payment. Please try again later."
                );
            }

            $ledgerEntryData = [
                'entry_type' => LedgerEntryType::DEBIT->value,
                'transaction_id' => $transactionId,
                'transaction_type' => TransactionType::REFUND->value,
                'entry_category' => EntryCategory::REFUND_AMOUNT->value,
                'payment_type' => $payment['payment_type'],
                'payment_id' => $paymentId, 
                'withdrawal_id' => null,
                'payout_id' => null,
                'amount' => $payment['total_amount'],
                'notes' => 'Refunded/Voided transaction for payment ' . $payment['public_payment_id']
            ];

            $isLedgerEntryInserted = createLedgerEntry($conn, $ledgerEntryData);

            if(!$isLedgerEntryInserted) {
                throw new CustomException(
                    null,
                    "Failed to create ledger entry for refund.",
                    ExceptionTypes::INTERNAL_SERVER_ERROR,
                    ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                    "Failed to process refund. Please try again later."
                );
            }
        }

        $conn->commit();

        // INTEGRATION REFACTOR: Return whatever the higher order function needs
        // right now just return true for success
        return true;
    } catch(Exception $e) {
        // check if error is xendit payment voided/refunded, if so, do not rollback db changes
        // as the payment has already been voided/refunded in xendit
        $conn->rollback();

        if($e instanceof CustomException) throw $e;

        throw new CustomException(
            $e,
            $e->getMessage(),
            ExceptionTypes::INTERNAL_SERVER_ERROR,
            ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
            "Failed to cancel payment of service. Please try again later."
        );
    }

}

function generatePaymentCheckoutLink($checkoutDetails) {
    $httpClient = generateXenditHttpClient();

    $mobileNumStr = isset($checkoutDetails["customer"]["mobile_number"]) ? $checkoutDetails["customer"]["mobile_number"] : "null";

    $data = [
        "external_id" => $checkoutDetails['external_transaction_id'],
        "amount" => $checkoutDetails['amount'],
        "invoice_duration" => 86400,
        "customer" => [
            "given_names" => $checkoutDetails["customer"]["given_names"],
            "surname" => $checkoutDetails["customer"]["surname"],
            "email" => $checkoutDetails["customer"]["email"],
            "mobile_number" => $mobileNumStr,
        ],
        "callback_url" => $checkoutDetails["callback_url"],
        "success_redirect_url" => $checkoutDetails["success_redirect_url"],
        "failure_redirect_url" => $checkoutDetails["failure_redirect_url"],
        "currency" => Currency::PHP->value,
        "payment_methods" => [
            "GCASH",
            "PAYMAYA"
        ]
    ];

    // add additional fields if needed
    if(isset($checkoutDetails['metadata'])) $data['metadata'] = [...$checkoutDetails['metadata']];
        
    $jsonBody = json_encode($data);

    $res = $httpClient->post(XENDIT_INVOICES_ENDPOINT, [
        'body' => $jsonBody
    ]);

    $responseData = json_decode($res->getBody(), true);
    
    return $responseData['invoice_url'];
}

function reverseEwalletPayment($providerTransactionRefId) {
    $httpClient = generateXenditHttpClient();

    // get ewallet charge first
    $ewalletCharge = getEwalletCharge($providerTransactionRefId);

    if(empty($ewalletCharge) || !isset($ewalletCharge['status'])) {
        throw new CustomException(
            null,
            "Ewallet charge not found.",
            ExceptionTypes::XENDIT_EWALLET_CHARGE_NOT_FOUND,
            ExceptionTypes::XENDIT_EWALLET_CHARGE_NOT_FOUND->toErrorData()['statusCode'], 
            ExceptionTypes::XENDIT_EWALLET_CHARGE_NOT_FOUND->toErrorData()['message']
        );
    }

    $ewcVoidStatus = $ewalletCharge['void_status'];
    $ewcStatus = $ewalletCharge['status'];
    $ewcChargedAmount = $ewalletCharge['payer_charged_amount'];

    // check settlement status (through void status and payer charged amount)
    // there is no direct settlement status field in ewallet charge object
    // SETTLED transaction cannot be voided and must be refunded
    // UNSETTLED can be voided but cannot be refunded

    if($ewcVoidStatus === XenditTransactionStatus::SUCCEEDED->value || $ewcStatus === XenditTransactionStatus::REFUNDED->value) {
        // ewc has been voided already
        throw new CustomException(
            null,
            "Ewallet charge has already been voided.",
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_VOIDED,
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_VOIDED->toErrorData()['statusCode'], 
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_VOIDED->toErrorData()['message']
        );
    }

    if($ewcStatus === XenditTransactionStatus::REFUNDED->value) {
        // ewc has been refunded already
        throw new CustomException(
            null,
            "Ewallet charge has already been refunded.",
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_REFUNDED,
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_REFUNDED->toErrorData()['statusCode'], 
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_REFUNDED->toErrorData()['message']
        );
    }

    $isReversalSuccessful = false;

    if($ewcVoidStatus === null && $ewcChargedAmount == null) {
        // ewc has not been settled yet, can be voided, set url as xendit void endpoint
        $voidRes = voidEwalletCharge($providerTransactionRefId);

        // for development/testing purposes, consider void successful if status PENDING
        if($voidRes['status'] === XenditTransactionStatus::VOIDED->value || $voidRes['void_status'] === XenditTransactionStatus::PENDING->value) {
            $isReversalSuccessful = true;
        }
    }

    if($ewcChargedAmount !== null || $ewcChargedAmount > 0) {
        // ewc has been settled already, must be refunded instead
        $refundRes = refundEwalletCharge($providerTransactionRefId);

        // for development/testing purposes, consider refund successful if status PENDING
        if($refundRes['status'] === XenditTransactionStatus::REFUNDED->value || $refundRes['void_status'] === XenditTransactionStatus::PENDING->value) {
            $isReversalSuccessful = true;
        }
    }

    if(!$isReversalSuccessful) {
        throw new CustomException(
            null,
            "Failed to reverse e-wallet charge in Xendit.",
            ExceptionTypes::INTERNAL_SERVER_ERROR,
            ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
            "Failed to reverse e-wallet charge in Xendit."
        );
    }

    return $isReversalSuccessful;
}

function getEwalletCharge($providerTransactionRefId) {
    $httpClient = generateXenditHttpClient();
    $url = XENDIT_EWALLET_CHARGE_ENDPOINT . "/" . $providerTransactionRefId;
    $res = $httpClient->get($url);
    $decodedResData = json_decode($res->getBody(), true);

    return $decodedResData;
}

function voidEwalletCharge($providerTransactionRefId) {
    // SAMPLE RESPONSE FROM VOIDING AN EWALLET CHARGE
    // {
    //     "id": "ewc_e06b4b77-8eb2-419b-80c3-815fd442bdfa",
    //     "business_id": "68c3f7135d83025f14eda7fa",
    //     "reference_id": "EX-TXN-EVRTO1GYQ8KT",
    //     "status": "SUCCEEDED",
    //     "currency": "PHP",
    //     "charge_amount": 134.4,
    //     "capture_amount": 134.4,
    //     "payer_charged_currency": null,
    //     "payer_charged_amount": null,
    //     "refunded_amount": null,
    //     "checkout_method": "ONE_TIME_PAYMENT",
    //     "channel_code": "PH_GCASH",
    //     "channel_properties": {
    //         "success_redirect_url": "https://checkout-staging.xendit.co/web/691afdae8f4ff6877baf89ed/processing",
    //         "failure_redirect_url": "https://checkout-staging.xendit.co/web/691afdae8f4ff6877baf89ed#failed",
    //         "cancel_redirect_url": "https://checkout-staging.xendit.co/web/691afdae8f4ff6877baf89ed#cancelled",
    //         "pending_redirect_url": "https://checkout-staging.xendit.co/web/691afdae8f4ff6877baf89ed"
    //     },
    //     "actions": {
    //         "desktop_web_checkout_url": "https://ewallet-mock-connector.xendit.co/v1/ewallet_connector/checkouts?token=d4dfrd7s4ads73em3c2g",
    //         "mobile_web_checkout_url": "https://ewallet-mock-connector.xendit.co/v1/ewallet_connector/checkouts?token=d4dfrd7s4ads73em3c2g",
    //         "mobile_deeplink_checkout_url": null,
    //         "qr_checkout_string": null
    //     },
    //     "is_redirect_required": true,
    //     "callback_url": "https://payments-processor-dev-pci.ap-southeast-1.tidnex.com/payments/ewallet/pr-2ce9ec24-e510-4ae1-93d1-6a36184def3e",
    //     "created": "2025-11-17T10:49:24.42712Z",
    //     "updated": "2025-11-17T10:54:11.292122Z",
    //     "void_status": "PENDING",
    //     "voided_at": null,
    //     "capture_now": true,
    //     "customer_id": null,
    //     "customer": null,
    //     "payment_method_id": null,
    //     "failure_code": null,
    //     "basket": null,
    //     "metadata": {
    //         "_invoice": {
    //             "id": "691afdae8f4ff6877baf89ed"
    //         },
    //         "publc_payment_id": "PAY-2RK3SGIPHUDH",
    //         "public_payment_session_id": "PAY-SESS-VEFUFXRMAKPT",
    //         "service_id": 14,
    //         "service_type": "ride_booking"
    //     },
    //     "shipping_information": null,
    //     "internal_metadata": {
    //         "invoice": {
    //             "id": "691afdae8f4ff6877baf89ed"
    //         },
    //         "payments_api": {
    //             "id": "pr-2ce9ec24-e510-4ae1-93d1-6a36184def3e"
    //         }
    //     },
    //     "payment_detail": {
    //         "fund_source": null,
    //         "source": null
    //     }
    // }
    $httpClient = generateXenditHttpClient();
    $url = XENDIT_EWALLET_CHARGE_ENDPOINT . "/" . $providerTransactionRefId . XENDIT_VOID_ENDPOINT;
    $res = $httpClient->post($url);
    $decodedResData = json_decode($res->getBody(), true);

    return $decodedResData;
}

function refundEwalletCharge($providerTransactionRefId) {
    $httpClient = generateXenditHttpClient();
    $url = XENDIT_EWALLET_CHARGE_ENDPOINT . "/" . $providerTransactionRefId . XENDIT_REFUND_ENDPOINT;
    $res = $httpClient->post($url);
    $decodedResData = json_decode($res->getBody(), true);

    return $decodedResData;
}

function completeRideBookingPayments($serviceId) {
    $conn = getDbConnection();

    try {
        $paymentSession = getPaymentSessionWithServiceId($conn, $serviceId, ServiceType::RIDE_BOOKING->value);

        if(empty($paymentSession)) {
            throw new CustomException(
                null,
                'Failed to find payment session for service with id ' . $serviceId,
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND,
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND->toErrorData()['message']
            );
        }

        $payment = getPaymentById($conn, $paymentSession['payment_id']);
        $isPaymentCompleted = false;

        if($payment['payment_method'] === PaymentMethod::CASH->value) {
            // offline payment
            $isPaymentCompleted = completeRideBookingOfflinePayment($serviceId);
        } else {
            // online payment
            $isPaymentCompleted = completeRideBookingOnlinePayment($serviceId);
        }

       $invoiceRes = generateInvoiceForRideBooking($serviceId);
       return $invoiceRes;
    } catch(Exception $e) {
        echo "Error completing ride booking payments: " . $e->getMessage();
        echo "<br>";
        echo $e->getTraceAsString();
        echo "<br>";
        echo "<br>";
        echo $e->getResponse()->getBody()->getContents();
        exit;
    }

} 

function completeRideBookingOnlinePayment($serviceId) {
    $isInternalTransferSuccessful = false;

    try {
        // CREATE PAYOUT INTENT
        $conn = getDbConnection();
        $conn->autocommit(FALSE);
        $conn->begin_transaction();

        $booking = getRideBookingById($conn, $serviceId);
        $bookingId = $booking['booking_id'];
        $publicBookingId = $booking['public_booking_id'];
        $serviceType = ServiceType::RIDE_BOOKING->value;

        $paymentSession = getPaymentSessionWithServiceId($conn, $booking['booking_id'],  $serviceType);
        $paymentSessId = $paymentSession['payment_session_id'];

        $payment = getPaymentById($conn, $paymentSession['payment_id']);
        $paymentId = $payment['payment_id'];

        $transaction = getTransactionById($conn, $paymentSession['transaction_id']);
        $transactionId = $transaction['transaction_id'];

        $serviceProvider = getUserAccById($conn, $booking['driver_id']);
        $serviceProviderUserId = $serviceProvider['user_id'];

        $platformAccount = getMasterAccount($conn, XENDIT_MASTER_ACCOUNT_NUM);
        $serviceProviderAcc = getPlatformSubAccountByUserId(
            $conn, 
            $serviceProviderUserId, 
            UserType::DRIVER->value
        );

        $amountToTransfer = convertStrToFloat($payment['net_pay']);
        $publicPayoutId = Prefix::PAYOUT->value . "-" . strtoupper(generateNanoId(12));
        $publicTransactionId = Prefix::TRANSACTION->value . "-". strtoupper(generateNanoId(12));
        $externalTransactionId = Prefix::EXTERNAL_TRANSACTION->value . "-". strtoupper(generateNanoId(12));

        $payoutDetails = [
            'public_payout_id' => $publicPayoutId,
            'service_type' => ServiceType::RIDE_BOOKING->value,
            'service_id' => $bookingId,
            'paid_to' => $serviceProviderUserId,
            'total_payout_amount' => $amountToTransfer,
            'payment_id' => $payment['payment_id'],
            'source_account_id' => $platformAccount['platform_account_id'],
            'destination_account_id' => $serviceProviderAcc['platform_account_id'],
            'payout_status' => TransactionStatus::PAID->value
        ];


        $txnDetails = [
            'public_transaction_id' => $publicTransactionId,
            'paid_by' => null, // user making the payment (basic user)
            'paid_to' => $serviceProviderUserId, 
            'transaction_method' => PaymentMethod::XENDIT_INTERNAL_TRANSFER->value,
            'transaction_channel' => PaymentChannel::XENDIT_INTERNAL_TRANSFER->value,
            'external_transaction_id' => $externalTransactionId,
            'source_account_id' => $serviceProviderAcc['platform_account_id'],
            'source_account_type' => TransactionSourceType::PLATFORM_ACCOUNT->value,
            'destination_account_id' => $platformAccount['platform_account_id'],
            'destination_account_type' => TransactionSourceType::PLATFORM_ACCOUNT->value,
            'total_amount_paid' => $amountToTransfer,
            'transaction_status' => TransactionStatus::PAID->value,
        ];

        // update status of all payment related records to PAID
        // nah bruh, passing conn to another service method is diabolical hajkaksdad
        updatePaymentSessionStatus($conn, $paymentSessId, TransactionStatus::PAID->value);
        updatePaymentStatus($conn, $paymentId, TransactionStatus::PAID->value);
        updateTransactionStatus($conn, $transactionId, TransactionStatus::PAID->value);

        $ledgerEntriesForPayment = array(
            [
                'entry_type' => LedgerEntryType::CREDIT->value,
                'transaction_id' => $transactionId,
                'transaction_type' => TransactionType::PAYMENT->value,
                'entry_category' => EntryCategory::NET_PAY->value,
                'payment_type' => $payment['payment_type'],
                'payment_id' => $paymentId, 
                'withdrawal_id' => null,
                'payout_id' => null,
                'amount' => $payment['net_pay'],
                'notes' => 'Net pay for payment for ride booking ' . $booking['public_booking_id']
            ],
            [
                'entry_type' =>  LedgerEntryType::CREDIT->value,
                'transaction_id' => $transactionId,
                'transaction_type' => TransactionType::PAYMENT->value,
                'entry_category' => EntryCategory::TAX_PAY->value,
                'payment_type' => $payment['payment_type'],
                'payment_id' => $paymentId,
                'withdrawal_id' => null,
                'payout_id' => null,
                'amount' => $payment['tax_pay'],
                'notes' => 'Tax pay for payment for ride booking ' . $booking['public_booking_id']
            ],
            [
                'entry_type' =>  LedgerEntryType::CREDIT->value,
                'transaction_id' => $transactionId,
                'transaction_type' => TransactionType::PAYMENT->value,
                'entry_category' => EntryCategory::PLATFORM_FEE->value,
                'payment_type' => $payment['payment_type'],
                'payment_id' => $paymentId,
                'withdrawal_id' => null,
                'payout_id' => null,
                'amount' => $payment['platform_fee'],
                'notes' => 'Platform fee for payment for ride booking ' . $booking['public_booking_id']
            ],
        );

        $isLedgerForPaymentInserted = createLedgerEntries($conn, $ledgerEntriesForPayment);

        if(!$isLedgerForPaymentInserted) {
            throw new CustomException(
                null,
                'Failed to create ledger entries for tax pay, net pay, and platform fee with for payment ' . $paymentId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
        
        // transfer first payout to service provider via xendit
        $transferPayload = [
            'amount' => $amountToTransfer,
            'currency' => 'PHP',
            'destination_user_id' => $serviceProviderAcc['external_account_id'],
            'source_user_id' => $platformAccount['external_account_id'],
            'reference' => $externalTransactionId
        ];

        // transfer funds to driver via xendit
        $transferRes = transferToXenditAccount($transferPayload);

        $transferResStatus = $transferRes['status'];

        if($transferResStatus === XenditTransactionStatus::FAILED->value) {
            throw new CustomException(
                null,
                'Failed to transfer payout funds to driver for payment session id ' . $paymentSessionId,
                ExceptionTypes::INTERNAL_TRANSFER_FAILED,
                ExceptionTypes::INTERNAL_TRANSFER_FAILED->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_TRANSFER_FAILED->toErrorData()['message']
            );
        }

        $isInternalTransferSuccessful = true;

        $providerTxnRefId = $transferRes['transfer_id'];

        $isTxnInserted = addNewTransaction($conn, $txnDetails);

        if(!$isTxnInserted) {
            throw new CustomException(
                null,
                'Failed to create payout transaction for payment session id ' . $paymentSessionId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $newTxnId = $conn->insert_id;

        setProviderRefIdOfTxn(
            $conn, 
            $newTxnId, 
            $providerTxnRefId
        );

        $txnForPayout = getTransactionById($conn, $newTxnId);

        $payoutDetails['transaction_id'] = $newTxnId;

        $isPayoutInserted = addNewPayout($conn, $payoutDetails);

        if(!$isPayoutInserted) {
            throw new CustomException(
                null,
                'Failed to create payout record for payment session id ' . $paymentSessionId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $payoutId = $conn->insert_id;
        $payout = getPayoutById($conn, $payoutId); 

        // add ledger entry for payout
        $entryNote = 'Service payout for ' 
                    . ServiceType::fromString($serviceType)->toReadableString()
                    . " "
                    . $publicBookingId;

        $ledgerEntryData = [
            'entry_type' => LedgerEntryType::DEBIT->value,
            'transaction_id' => $newTxnId,
            'transaction_type' => TransactionType::PAYOUT->value,
            'entry_category' => PaymentType::PAYOUT_AMOUNT->value,
            'payment_type' => null,
            'payout_id' => $payoutId, 
            'withdrawal_id' => null,
            'payment_id' => null,
            'amount' => $payout['total_payout_amount'],
            'notes' => $entryNote
        ];

        $isLedgerEntryInserted = createLedgerEntry($conn, $ledgerEntryData);

        if(!$isLedgerEntryInserted) {
            throw new CustomException(
                null,
                'Failed to create ledger entry for payout transaction id ' . $payoutTransactionId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $netEarnings = convertStrToFloat($payment['net_pay']);

        $driverEarnData = [
            "user_id" => $serviceProviderUserId,
            "payment_id" => $payment['payment_id'], 
            "service_type" => $serviceType,
            "service_id" => $bookingId,
            "amount_earned" => $netEarnings
        ];

        $isDriverEarningInserted = addNewDriverEarningRecord($conn, $driverEarnData);

        if(!$isDriverEarningInserted) {
            throw new CustomException(
                null,
                'Failed to add driver earning record for user with id ' . $serviceProviderUserId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $currentAccBalOfServiceProvider = getBalanceOfAccount($conn, $serviceProviderAcc['platform_account_id']);
        $currentAccBalOfPlatform = getBalanceOfAccount($conn, $platformAccount['platform_account_id']);

        $taxCollected = convertStrToFloat($payment['tax_pay']);
        $platformFee = convertStrToFloat($payment['platform_fee']);
        $totalRevenue = $taxCollected + $platformFee;
        
        $amtToAddToServiceProviderAcc = $netEarnings;
        $amtToAddToPlatformAcc = $taxCollected + $platformFee;

        $platformRevenue = [
            'transaction_id' => $transactionId,
            'payment_id' => $payment['payment_id'],
            'service_type' => $serviceType,
            'service_id' => $bookingId,
            'platform_fee' => $platformFee,
            'tax_amount' => $taxCollected,
            'total_revenue' => $totalRevenue
        ];

        $isServiceProviderAccUpdated = addToAccountBalance(
            $conn,
            $serviceProviderAcc['platform_account_id'],
            $amtToAddToServiceProviderAcc
        );

        $isPlatformAccUpdated = addToAccountBalance(
            $conn,
            $platformAccount['platform_account_id'],
            $amtToAddToPlatformAcc
        );

        $isPlatformRevenueRecorded = addNewPlatformRevenueRecord(
            $conn,
            $platformRevenue
        );

        if(
            !$isServiceProviderAccUpdated || 
            !$isPlatformAccUpdated || 
            !$isPlatformRevenueRecorded
        ) {
            throw new CustomException(
                null,
                'Failed to update account balances and/or record platform revenue for payment session id ' . $paymentSessionId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $conn->commit();
        return true;
    } catch(Exception $e) {
        $conn->rollback();

        if(!$isInternalTransferSuccessful) {
            $reversedTransferRes = transferToXenditAccount([
                'amount' => $amountToTransfer,
                'currency' => 'PHP',
                'destination_user_id' => $platformAccount['external_account_id'],
                'source_user_id' => $serviceProviderAcc['external_account_id'],
                'reference' => $externalTransactionId
            ]);

            $reversedTransferResStatus = $reversedTransferRes['status'];

            if($reversedTransferResStatus !== XenditTransactionStatus::SUCCESSFUL->value) {
                $errDesc = 'Failed to reverse transfer from ' 
                            . $serviceProviderAcc['external_account_id'] 
                            . ' to ' . $platformAccount['external_account_id'] 
                            . ' after payout processing failure for payment session id ' 
                            . $paymentSessionId;
                $e = new CustomException(
                    $e,
                    $errDesc,
                    ExceptionTypes::INTERNAL_TRANSFER_FAILED,
                    ExceptionTypes::INTERNAL_TRANSFER_FAILED->toErrorData()['statusCode'], 
                    ExceptionTypes::INTERNAL_TRANSFER_FAILED->toErrorData()['message']
                );
            }
        }
        
        echo "Error completing payment: " . $e->getMessage();
        echo "<br>";
        echo $e->getTraceAsString();
        echo "<br>";
        echo "<br>";
        print_r($e);
        exit;
    }
}

// cash payment completion
function completeRideBookingOfflinePayment($serviceId) {
    try {
        $conn = getDbConnection();

        $conn->autocommit(FALSE);
        $conn->begin_transaction();

        $booking = getRideBookingById($conn, $serviceId);
        $bookingId = $booking['booking_id'];
        $serviceType = ServiceType::RIDE_BOOKING->value;
        $serviceProvider = getUserAccById($conn, $booking['driver_id']);
        $serviceProviderUserId = $serviceProvider['user_id'];

        $paymentSession = getPaymentSessionWithServiceId($conn, $booking['booking_id'], $serviceType);
        $paymentSessId = $paymentSession['payment_session_id'];

        $payment = getPaymentById($conn, $paymentSession['payment_id']);
        $paymentId = $payment['payment_id'];

        $transaction = getTransactionById($conn, $paymentSession['transaction_id']);
        $transactionId = $transaction['transaction_id'];

        updatePaymentSessionStatus($conn, $paymentSessId, TransactionStatus::PAID->value);
        updatePaymentStatus($conn, $paymentId, TransactionStatus::PAID->value);
        updateTransactionStatus($conn, $transactionId, TransactionStatus::PAID->value);

        $ledgerEntryForPayout = [
            'entry_type' => LedgerEntryType::CREDIT->value,
            'transaction_id' => $transactionId,
            'transaction_type' => TransactionType::PAYMENT->value,
            'entry_category' => EntryCategory::TOTAL_AMOUNT->value,
            'payment_type' => $payment['payment_type'],
            'payment_id' => $paymentId, 
            'withdrawal_id' => null,
            'payout_id' => null,
            'amount' => $payment['total_amount'],
            'notes' => 'Cash payment received for ride booking ' . $booking['public_booking_id']
        ];

        $isLedgerForPayoutInserted = createLedgerEntry($conn, $ledgerEntryForPayout);

        if(!$isLedgerForPayoutInserted) {
            throw new CustomException(
                null,
                'Failed to create ledger entry for payout transaction id ' . $payoutTransactionId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $netEarnings = convertStrToFloat($payment['net_pay']);

        $driverEarnData = [
            "user_id" => $serviceProviderUserId,
            "payment_id" => $payment['payment_id'], 
            "service_type" => $serviceType,
            "service_id" => $bookingId,
            "amount_earned" => $netEarnings
        ];

        $isDriverEarningInserted = addNewDriverEarningRecord($conn, $driverEarnData);

        if(!$isDriverEarningInserted) {
            throw new CustomException(
                null,
                'Failed to record earning of driver for ride booking ' . $booking['public_booking_id'],
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
        
        $conn->commit();
        return true;
    } catch(Exception $e) {
        echo "Error completing payment: " . $e->getMessage();
        echo "<br>";
        echo $e->getTraceAsString();
        echo "<br>";
        echo "<br>";
        print_r($e);
        exit;
    }
}


// for completing cash balance payments for car rentals ONLY
// pending balance will be marked as paid once the owner 
// has confirmed that they have received cash payment.
function processCashBalancePayment($paymentSessId) {
    try {
        $conn = getDbConnection();
        
        $paymentSession = getPaymentSessionById($conn, $paymentSessId);
        $transaction = getTransactionById($conn, $paymentSession['transaction_id']);
        $payment = getPaymentById($conn, $paymentSession['payment_id']);

        if(empty($paymentSession) || empty($transaction) || empty($payment)) {
            throw new CustomException(
                null,
                'Failed to find payment/session/transaction for payment session with id ' . $paymentSessId,
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND,
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::PAYMENT_SESSION_NOT_FOUND->toErrorData()['message']
            );
        }

        $serviceId = $paymentSession['service_id'];
        $serviceType = $paymentSession['service_type'];
        $service = getCarRentalById($conn, $serviceId);
        $serviceProvider = getUserAccById($conn, $service['driver_id']);
        $serviceProviderUserId = $serviceProvider['user_id'];

        if(empty($serviceProvider)) {
            throw new CustomException(
                null,
                'Failed to find service provider for car rental with id ' . $serviceId,
                ExceptionTypes::USER_NOT_FOUND,
                ExceptionTypes::USER_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::USER_NOT_FOUND->toErrorData()['message']
            );
        }

        $amountEarned = convertStrToFloat($payment['net_pay']);
        $amountTax = convertStrToFloat($payment['tax_pay']);
        $amountPlatformFee = convertStrToFloat($payment['platform_fee']);

        $userPlatformAcc = getPlatformSubAccountByUserId(
            $conn, 
            $serviceProviderUserId, 
            UserType::DRIVER->value
        );

        if(empty($userPlatformAcc)) {
            throw new CustomException(
                null,
                'Failed to find platform account for user with id ' . $serviceProviderUserId,
                ExceptionTypes::PLATFORM_ACCOUNT_NOT_FOUND,
                ExceptionTypes::PLATFORM_ACCOUNT_NOT_FOUND->toErrorData()['statusCode'], 
                ExceptionTypes::PLATFORM_ACCOUNT_NOT_FOUND->toErrorData()['message']
            );
        }

        $userPlatformAccId = $userPlatformAcc['platform_account_id'];

        $conn->autocommit(FALSE);
        $conn->begin_transaction();

        $isPaymentSessUpdated = updatePaymentSessionStatus($conn, $paymentSession['payment_session_id'], TransactionStatus::PAID->value);
        $isPaymentUpdated = updatePaymentStatus($conn, $payment['payment_id'], TransactionStatus::PAID->value);
        $isTransactionUpdated = updateTransactionStatus($conn, $transaction['transaction_id'], TransactionStatus::PAID->value);

        $ledgerEntryData = [
            'entry_type' => LedgerEntryType::CREDIT->value,
            'transaction_id' => $transaction['transaction_id'],
            'transaction_type' => TransactionType::PAYMENT->value,
            'entry_category' => EntryCategory::TOTAL_AMOUNT->value,
            'payment_type' => $payment['payment_type'],
            'payment_id' => $payment['payment_id'], 
            'withdrawal_id' => null,
            'payout_id' => null,
            'amount' => $payment['total_amount'],
            'notes' => 'Cash balance payment received for service id ' . $paymentSession['service_id']
        ];

        $isLedgerEntryInserted = createLedgerEntry($conn, $ledgerEntryData);

        if(!$isLedgerEntryInserted) {
            throw new CustomException(
                null,
                'Failed to create ledger entry for payment session id ' . $paymentSessId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
 
        $driverEarnData = [
            "user_id" => $serviceProviderUserId,
            "payment_id" => $payment['payment_id'], 
            "service_type" => $serviceType,
            "service_id" => $serviceId,
            "amount_earned" => $amountEarned
        ];

        $isDriverEarningInserted = addNewDriverEarningRecord($conn, $driverEarnData);

        if(!$isDriverEarningInserted) {
            throw new CustomException(
                null,
                'Failed to add driver earning record for user with id ' . $serviceProviderUserId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $zeroBalanceAmt = 0.0;
        $balBufferAmt = 100.0; 
        $requiredMinimumBal = $amountTax + $amountPlatformFee + $balBufferAmt; 

        $currentAccBalance = getBalanceOfAccount($conn, $userPlatformAccId);
        
        if($currentAccBalance <= 0 || $currentAccBalance <= $requiredMinimumBal) {
            $pendingTaxDeduction = [
                "deduction" => [
                    "deduction_for" => EntryCategory::TAX_PAY->value,
                    "amount_to_deduct" => $amountTax
                ],
                "user" => [
                    "user_id" => $serviceProviderUserId,
                ],
                "platform_account" => [
                    "platform_account_id" => $userPlatformAccId
                ]
            ];
            $pendingPlatformFeeDeduction = [
                "deduction" => [
                    "deduction_for" => EntryCategory::PLATFORM_FEE->value,
                    "amount_to_deduct" => $amountPlatformFee
                ],
                "user" => [
                    "user_id" => $serviceProviderUserId,
                ],
                "platform_account" => [
                    "platform_account_id" => $userPlatformAccId
                ]
            ];

            $isPendingTaxDeductionInserted = addPendingDeductionForPlatformAcc($conn, $pendingTaxDeduction);
            $isPendingPlatformFeeDeductionInserted = addPendingDeductionForPlatformAcc($conn, $pendingPlatformFeeDeduction);

            if(!$isPendingTaxDeductionInserted || !$isPendingPlatformFeeDeductionInserted) {
                throw new CustomException(
                    null,
                    'Failed to add pending deductions for platform account id ' . $userPlatformAccId,
                    ExceptionTypes::INTERNAL_SERVER_ERROR,
                    ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                    ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
                );
            }
        } else {
            // TO DO: handle deduction from service provider from their account   
            // if insufficient balance, add pending deduction record
            // for background job to process later 
        }

        // generate invoice for the cash payment after updating all records
        $invoiceRes = generateInvoiceForRentalPayment($conn, $paymentSession["payment_session_id"]);
        if(empty($invoiceRes)) {
            throw new CustomException(
                null,
                'Failed to generate invoice for payment session id ' . $paymentSessId,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'], 
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }
        $invoiceId = $conn->insert_id;

        $conn->commit();
        return [
            "payment_session_id" => $paymentSession["payment_session_id"],
            "payment_id" => $payment["payment_id"],
            "transaction_id" => $transaction["transaction_id"],
            "invoice_id" => $invoiceId
        ];
    } catch(Exception $e){
        $conn->rollback();
        if($e instanceof CustomException) {
            $e->setErrorObj($e);
        }
        throw $e;
    }
}

?>