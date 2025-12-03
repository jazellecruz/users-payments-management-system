<?php 

enum PaymentMethod: string {
    case CASH = 'cash';
    case E_WALLET = 'e_wallet';
    case CARD = 'card';
    case XENDIT_INTERNAL_TRANSFER = 'xendit_internal_transfer';

    public function toReadableString(): string {
        return match($this) {
            PaymentMethod::CASH => 'Cash',
            PaymentMethod::E_WALLET => 'E-Wallet',
            PaymentMethod::CARD => 'Card',
            PaymentMethod::XENDIT_INTERNAL_TRANSFER => 'Xendit Internal Transfer',
        };
    }

    public static function fromString(string $value): ?PaymentMethod {
        return match($value) {
            'cash' => PaymentMethod::CASH,
            'e_wallet' => PaymentMethod::E_WALLET,
            'card' => PaymentMethod::CARD,
            'xendit_internal_transfer' => PaymentMethod::XENDIT_INTERNAL_TRANSFER,
            default => null,
        };
    }
}

?>