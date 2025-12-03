<?php 

enum PlatformAccountType: string {
    case SUB_ACCOUNT = 'sub_account';
    case MASTER_ACCOUNT = 'master_account';

    public function toReadableString(): string {
        return match($this) {
            PlatformAccountType::MASTER_ACCOUNT => 'Master Account',
            PlatformAccountType::SUB_ACCOUNT => 'Sub Account',
        };
    }

    public static function fromString(string $value): ?PlatformAccountType {
        return match($value) {
            'master_account' => PlatformAccountType::MASTER_ACCOUNT,
            'sub_account' => PlatformAccountType::SUB_ACCOUNT,
            default => null,
        };
    }
}

?>