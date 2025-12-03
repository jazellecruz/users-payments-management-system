<?php 

enum LedgerEntryType: string {
    case DEBIT = 'debit';
    case CREDIT = 'credit';

    public function toReadableString(): string {
        return match($this) {
            LedgerEntryType::DEBIT => 'Debit',
            LedgerEntryType::CREDIT => 'Credit',
        };
    }

    public static function fromString(string $value): ?LedgerEntryType {
        return match($value) {
            'debit' => LedgerEntryType::DEBIT,
            'credit' => LedgerEntryType::CREDIT,
            default => null,
        };
    }
}

?>