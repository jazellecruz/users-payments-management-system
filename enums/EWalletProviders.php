<?php 

enum EWalletProviders: string {
    case G_CASH = 'g_cash';
    case PAYMAYA = 'paymaya';

    public function toReadableString(): string {
        return match($this) {
            EWalletProviders::G_CASH => 'GCash',
            EWalletProviders::PAYMAYA => 'PayMaya',
        };
    }

    public static function fromString(string $value): ?EWalletProviders {
        return match($value) {
            'g_cash' => EWalletProviders::G_CASH,
            'paymaya' => EWalletProviders::PAYMAYA,
            default => null,
        };
    }
}

?>