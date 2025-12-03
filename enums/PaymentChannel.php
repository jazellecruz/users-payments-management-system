<?php 

enum PaymentChannel: string {
    case G_CASH = 'g_cash';
    case PAYMAYA = 'paymaya';
    case CASH = 'cash';
    case CARD = 'card';
    case XENDIT_INTERNAL_TRANSFER = 'xendit_internal_transfer';

    public function toReadableString(): string {
        return match($this){
            PaymentChannel::G_CASH => 'GCash',
            PaymentChannel::PAYMAYA => 'PayMaya',
            PaymentChannel::CASH => 'Cash',
            PaymentChannel::CARD => 'Card',
            PaymentChannel::XENDIT_INTERNAL_TRANSFER => 'Xendit Internal Transfer',
        };
    }

    public static function fromString(string $value): ?PaymentChannel {
        return match($value) {
            'g_cash' => PaymentChannel::G_CASH,
            'paymaya' => PaymentChannel::PAYMAYA,
            'cash' => PaymentChannel::CASH,
            'card' => PaymentChannel::CARD,
            'xendit_internal_transfer' => PaymentChannel::XENDIT_INTERNAL_TRANSFER,
        };
    }
}
?>