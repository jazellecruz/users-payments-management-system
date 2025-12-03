<?php 

enum SourceType: string {
    case E_WALLET = 'e_wallet';
    case CASH = 'cash';
    case PLATFORM_ACCOUNT = 'platform_account';

    public function toReadableString(): string {
        return match($this) {
            SourceType::E_WALLET => 'E-Wallet',
            SourceType::CASH => 'Cash',
            SourceType::PLATFORM_ACCOUNT => 'Platform Account',
        };
    }

    public static function fromString(string $value): ?SourceType {
        return match($value) {
            'e_wallet' => self::E_WALLET,
            'cash' => self::CASH,
            'platform_account' => self::PLATFORM_ACCOUNT,
            default => null,
        };
    }
}

?>