<?php 

enum TransactionStatus: string {
    case PENDING = 'pending';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case REJECTED = 'rejected';
    case PARTIALLY_PAID = 'partially_paid';
    case COLLECTED = 'collected';
    case VOIDED = 'voided';
    case TRANSFERRED = 'transferred';

    public function toReadableString(): string {
        return match($this) {
            TransactionStatus::PENDING => 'Pending',
            TransactionStatus::PAID => 'Paid',
            TransactionStatus::CANCELLED => 'Cancelled',
            TransactionStatus::REFUNDED => 'Refunded',
            TransactionStatus::REJECTED => 'Rejected',
            TransactionStatus::PARTIALLY_PAID => 'Partially Paid',
            TransactionStatus::COLLECTED => 'Collected',
            TransactionStatus::VOIDED => 'Voided',
            TransactionStatus::PENDING => 'Transfered',
        };
    }

    public static function fromString(string $value): ?TransactionStatus {
        return match($value) {
            'pending' => TransactionStatus::PENDING,
            'paid' => TransactionStatus::PAID,
            'cancelled' => TransactionStatus::CANCELLED,
            'refunded' => TransactionStatus::REFUNDED,
            'rejected' => TransactionStatus::REJECTED,
            'partially_paid' => TransactionStatus::PARTIALLY_PAID,
            'collected' => TransactionStatus::COLLECTED,
            'voided' => TransactionStatus::VOIDED,
            default => null,
        };
    }
}

?>