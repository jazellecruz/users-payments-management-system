<?php 

enum XenditTransactionStatus: string {
    case PAID = 'PAID';
    case EXPIRED = 'EXPIRED';
    case PENDING = 'PENDING';
    case FAILED = 'FAILED';
    case REFUNDED = 'REFUNDED';
    case VOIDED = 'VOIDED';
    case SUCCEEDED = 'SUCCEEDED';
}

?>