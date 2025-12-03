<?php 

enum PaymentType: string {
    case FULL_PAYMENT = 'full_payment';
    case DOWN_PAYMENT = 'down_payment';
    case BALANCE_PAYMENT = 'balance_payment';
    case TAX_PAYMENT = 'tax';
    case PALTFORM_FEE = 'platform_fee';
    case NET_PAY = 'net_pay';
    case TOTAL_AMOUNT = 'total_amount';
    case TRANSFER_FEE = 'transfer_fee';
    case WITHDRAWAL_AMOUNT = 'withdrawal_amount';
    case REFUND_AMOUNT = 'refund_amount';
    case PAYOUT_AMOUNT = 'payout_amount';

    public function toReadableString(): string {
        return match($this) {
            PaymentType::FULL_PAYMENT => 'Full Payment',
            PaymentType::DOWN_PAYMENT => 'Down Payment',
            PaymentType::BALANCE_PAYMENT => 'Balance Payment',
            PaymentType::TAX_PAYMENT => 'Tax Payment',
            PaymentType::PALTFORM_FEE => 'Platform Fee',
            PaymentType::NET_PAY => 'Net Pay',
            PaymentType::TOTAL_AMOUNT => 'Total Amount',
            PaymentType::TRANSFER_FEE => 'Transfer Fee',
            PaymentType::WITHDRAWAL_AMOUNT => 'Withdrawal Amount',
            PaymentType::REFUND_AMOUNT => 'Refund Amount',
            PaymentType::PAYOUT_AMOUNT => 'Payout Amount',
        };
    }

    public static function fromString(string $value): ?PaymentType {
        return match($value) {
            'full_payment' => PaymentType::FULL_PAYMENT,
            'down_payment' => PaymentType::DOWN_PAYMENT,
            'balance_payment' => PaymentType::BALANCE_PAYMENT,
            'tax' => PaymentType::TAX_PAYMENT,
            'platform_fee' => PaymentType::PALTFORM_FEE,
            'net_pay' => PaymentType::NET_PAY,
            'total_amount' => PaymentType::TOTAL_AMOUNT,
            'transfer_fee' => PaymentType::TRANSFER_FEE,
            'withdrawal_amount' => PaymentType::WITHDRAWAL_AMOUNT,
            'refund_amount' => PaymentType::REFUND_AMOUNT,
            'payout_amount' => PaymentType::PAYOUT_AMOUNT,
            default => null,
        };
    }
}

?>