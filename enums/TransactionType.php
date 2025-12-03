<?php 

enum TransactionType: string {
    case PAYMENT = 'payment';
    case PAYOUT = 'payout';
    case WITHDRAWAL = 'withdrawal';
    case REFUND = 'refund';

    public function toReadableString(): string {
        return match($this) {
            TransactionType::PAYMENT => 'Payment',
            TransactionType::PAYOUT => 'Payout',
            TransactionType::WITHDRAWAL => 'Withdrawal',
            TransactionType::REFUND => 'Refund',
        };
    }
}

?>