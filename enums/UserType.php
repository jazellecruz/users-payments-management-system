<?php 

enum UserType: string {
    case ADMIN = 'admin';
    case BASIC = 'basic';
    case DRIVER = 'driver';
    case BUS_REP = 'bus_rep';
    case PLATFORM = 'platform';

    public function toReadableString(): string {
        return match($this) {
            UserType::ADMIN => 'Admin',
            UserType::BASIC => 'Basic',
            UserType::DRIVER => 'Driver',
            UserType::BUS_REP => 'Business Representative',
            UserType::PLATFORM => 'Platform',
        };
    }

    public static function fromString(string $value): ?UserType {
        return match($value) {
            'admin' => UserType::ADMIN,
            'basic' => UserType::BASIC,
            'driver' => UserType::DRIVER,
            'bus_rep' => UserType::BUS_REP,
            'platform' => UserType::PLATFORM,
            default => null,
        };
    }
}

?>