<?php 

enum ExceptionTypes: string {
    case INTERNAL_SERVER_ERROR = "INTERNAL_SERVER_ERROR";
    case INVALID_EWALLET_ACCOUNT = "INVALID_EWALLET_ACCOUNT";
    case PAYMENT_ALREADY_COLLECTED = "PAYMENT_ALREADY_COLLECTED";
    case PAYMENT_NOT_FOUND = "PAYMENT_NOT_FOUND";
    case PAYMENT_SESSION_NOT_FOUND = "PAYMENT_SESSION_NOT_FOUND";
    case TRANSACTION_NOT_FOUND = "TRANSACTION_NOT_FOUND";
    case XENDIT_EWALLET_CHARGE_ALREADY_REFUNDED = "XENDIT_EWALLET_CHARGE_ALREADY_REFUNDED";
    case XENDIT_EWALLET_CHARGE_ALREADY_VOIDED = "XENDIT_EWALLET_CHARGE_ALREADY_VOIDED";
    case XENDIT_EWALLET_CHARGE_NOT_FOUND = "XENDIT_EWALLET_CHARGE_NOT_FOUND";
    case EXTERNAL_API_ERROR = "EXTERNAL_API_ERROR";
    case RUNTIME_EXCEPTION = "RUNTIME_EXCEPTION";
    case INTERNAL_TRANSFER_FAILED = "INTERNAL_TRANSFER_FAILED";
    
    public function toErrorData(): array {
        return match($this) {
            ExceptionTypes::INVALID_EWALLET_ACCOUNT => [
                'title' => 'Invalid E-Wallet Account',
                'message' => 'The provided e-wallet account is not valid.',
                'statusCode' => 400
            ],
            ExceptionTypes::INTERNAL_SERVER_ERROR => [
                'title' => 'Internal Server Error',
                'message' => 'An unexpected server error occurred. Please try again later.',
                'statusCode' => 500
            ],
            ExceptionTypes::PAYMENT_ALREADY_COLLECTED => [
                'title' => 'Payment Already Collected',
                'message' => 'The payment has already been collected.',
                'statusCode' => 400
            ],
            ExceptionTypes::PAYMENT_NOT_FOUND => [
                'title' => 'Payment Not Found',
                'message' => 'The requested payment could not be found.',
                'statusCode' => 404
            ],
            ExceptionTypes::PAYMENT_SESSION_NOT_FOUND => [
                'title' => 'Payment Session Not Found',
                'message' => 'The requested payment session could not be found.',
                'statusCode' => 404
            ],
            ExceptionTypes::TRANSACTION_NOT_FOUND => [
                'title' => 'Transaction Not Found',
                'message' => 'The requested transaction could not be found.',
                'statusCode' => 404
            ],
            ExceptionTypes::INTERNAL_TRANSFER_FAILED => [
                'title' => 'Internal Transfer Failed',
                'message' => 'The internal transfer of payments operation failed.',
                'statusCode' => 500
            ],
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_REFUNDED => [
                'title' => 'Xendit E-Wallet Charge Already Refunded',
                'message' => 'The Xendit e-wallet charge has already been refunded.',
                'statusCode' => 400
            ],
            ExceptionTypes::XENDIT_EWALLET_CHARGE_ALREADY_VOIDED => [
                'title' => 'Xendit E-Wallet Charge Already Voided',
                'message' => 'The Xendit e-wallet charge has already been voided.',
                'statusCode' => 400
            ],
            ExceptionTypes::XENDIT_EWALLET_CHARGE_NOT_FOUND => [
                'title' => 'Xendit E-Wallet Charge Not Found',
                'message' => 'The requested Xendit e-wallet charge could not be found.',
                'statusCode' => 404
            ],
            ExceptionTypes::RUNTIME_EXCEPTION => [
                'title' => 'Runtime Exception',
                'message' => 'A runtime exception occurred.',
                'statusCode' => 500
            ],
            ExceptionTypes::EXTERNAL_API_ERROR => [
                'title' => 'External API Error',
                'message' => 'An error occurred while communicating with an external API.',
                'statusCode' => 502
            ]
        };
    }
}

?>