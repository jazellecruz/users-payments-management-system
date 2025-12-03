<?php   

require_once __DIR__ . '/PaymentChannel.php';

// xendits payment channes
enum XenditPaymentChannel: string {
    case GCASH = 'GCASH';
    case PAYMAYA = 'PAYMAYA';
    
    // converts to local version of the payment channel enum
    public static function toLocalPaymentChannelEnum(string $channel): PaymentChannel {
        return match($channel) {
            XenditPaymentChannel::GCASH->value => PaymentChannel::G_CASH,
            XenditPaymentChannel::PAYMAYA->value => PaymentChannel::PAYMAYA,
        };
    }
}
?>