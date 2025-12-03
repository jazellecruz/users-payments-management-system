<?php 

enum ServiceType: string {
    case RIDE_BOOKING = 'ride_booking';
    case CAR_RENTAL = 'car_rental';

    public function toReadableString(): string {
        return match($this){
            ServiceType::RIDE_BOOKING => 'Ride Booking',
            ServiceType::CAR_RENTAL => 'Car Rental',
        };
    }

    public static function fromString(string $value): ?ServiceType {
        return match($value) {
            'ride_booking' => ServiceType::RIDE_BOOKING,
            'car_rental' => ServiceType::CAR_RENTAL,
            default => null,
        };
    }
}

?>