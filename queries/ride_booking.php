<?php 

function createBooking($conn, $bookingData) {
    $stmt = $conn->prepare(
    "INSERT INTO ride_bookings (
        public_booking_id, 
        booked_by, 
        driver_id, 
        pickup_location, 
        dropoff_location, 
        ride_status, 
        total_fare
    ) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param(
        "siisssd", 
        $bookingData['public_booking_id'], 
        $bookingData['booked_by'], 
        $bookingData['driver_id'], 
        $bookingData['pickup_location'], 
        $bookingData['dropoff_location'], 
        $bookingData['ride_status'], 
        $bookingData['total_fare']
    );
    return $stmt->execute();
}

function getRideBookingById($conn, $bookingId) {
    $stmt = $conn->prepare("SELECT * FROM ride_bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createCarRental($conn, $rentalData) {
    $stmt = $conn->prepare(
    "INSERT INTO car_rentals (
        public_rental_id, 
        user_id, 
        driver_id,
        car_model, 
        rent_start_date, 
        rent_end_date, 
        rental_status, 
        total_rental_cost
    ) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param(
        "siissssd", 
        $rentalData['public_rental_id'], 
        $rentalData['user_id'], 
        $rentalData['driver_id'], 
        $rentalData['car_model'], 
        $rentalData['rent_start_date'], 
        $rentalData['rent_end_date'], 
        $rentalData['rental_status'], 
        $rentalData['total_rental_cost']
    );
    return $stmt->execute();
}   

function getCarRentalById($conn, $rentalId) {
    $stmt = $conn->prepare("SELECT * FROM car_rentals WHERE rental_id = ?");
    $stmt->bind_param("i", $rentalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getRentalByPublicId($conn, $publicRentalId) {
    $stmt = $conn->prepare("SELECT * FROM car_rentals WHERE public_rental_id = ?");
    $stmt->bind_param("s", $publicRentalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateRideBookingStatus($conn, $bookingId, $newStatus) {
    $stmt = $conn->prepare("UPDATE ride_bookings SET ride_status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $newStatus, $bookingId);
    return $stmt->execute();
}

?>