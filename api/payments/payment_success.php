<?php 

require_once __DIR__ . '/../../enums/ServiceType.php';
require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../config/config.php';


if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $serviceType = $_GET["service_type"];
    $bookingId = null;
    $carRentalId = null;
    $redirectUrl = null;

    if($serviceType === ServiceType::RIDE_BOOKING->value) {
        $bookingId  = $_GET["public_service_id"];
        $redirectUrl = BASE_URL . "/during_booking.php?booking_id=" . $bookingId;
    } 

    if($serviceType === ServiceType::CAR_RENTAL->value) {
        $carRentalId = $_GET["public_service_id"];
        $redirectUrl = BASE_URL . "/during_car_rental.php?rental_id=" . $carRentalId;
    } 

    redirectUser($redirectUrl);
}

?>
