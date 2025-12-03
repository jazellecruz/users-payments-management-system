<?php 

enum Prefix: string {
    // USER RELATED
    case BUS_REP = 'BUS-REP';
    case DRIVER = 'DRV';
    case ADMIN = 'ADM';
    
    // TRANSACTION RELATED
    case SUB_ACCOUNT = 'ACC-SA';
    case MASTER_ACCOUNT = 'ACC-MA';
    case EWALLET = 'EWLT';
    case PAYMENT = 'PAY'; // change to pymt
    case WITHDRAWAL = 'WDL';
    case PAYOUT = 'PAY-OUT';
    case TRANSACTION = 'TXN';
    case EXTERNAL_TRANSACTION = 'EX-TXN';
    case PAYMENT_SESSION = 'PAY-SESS';
    case LEDGER_ENTRY = 'LDGR-ENT';
    case INVOICE = 'INV';

    // SERVICE RELATED
    case RIDE_BOOKING = 'RIDE-BKG';
    case CAR_RENTAL = 'CAR-RNT';
}

?>