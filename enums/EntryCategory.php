<?php 

enum EntryCategory: string {
    case TAX_PAY = 'tax_pay';
    case PLATFORM_FEE = 'platform_fee';
    case NET_PAY = 'net_pay';
    case TOTAL_AMOUNT = 'total_amount';
    case TRANSFER_FEE = 'transfer_fee';
    case WITHDRAWAL_AMOUNT = 'withdrawal_amount';
    case REFUND_AMOUNT = 'refund_amount';
    case PAYOUT_AMOUNT = 'payout_amount';

    public function toReadableString(): string {
        return match($this) {
            EntryCategory::TAX_PAY => 'Tax Pay',
            EntryCategory::PLATFORM_FEE => 'Platform Fee',
            EntryCategory::NET_PAY => 'Net Pay',
            EntryCategory::TOTAL_AMOUNT => 'Total Amount',
            EntryCategory::TRANSFER_FEE => 'Transfer Fee',
            EntryCategory::WITHDRAWAL_AMOUNT => 'Withdrawal Amount',
            EntryCategory::REFUND_AMOUNT => 'Refund Amount',
            EntryCategory::PAYOUT_AMOUNT => 'Payout Amount',
        };
    }

    public static function fromString(string $value): ?EntryCategory {
        return match($value) {
            'tax_pay' => EntryCategory::TAX_PAY,
            'platform_fee' => EntryCategory::PLATFORM_FEE,
            'net_pay' => EntryCategory::NET_PAY,
            'total_amount' => EntryCategory::TOTAL_AMOUNT,
            'transfer_fee' => EntryCategory::TRANSFER_FEE,
            'withdrawal_amount' => EntryCategory::WITHDRAWAL_AMOUNT,
            'refund_amount' => EntryCategory::REFUND_AMOUNT,
            'payout_amount' => EntryCategory::PAYOUT_AMOUNT,
            default => null,
        };
    }
}

?>