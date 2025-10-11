<?php 

function getAllBusinessRoles($conn) {
    $query = "SELECT * FROM business_rep_positions";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllBusinessTypes($conn) {
    $query = "SELECT * FROM business_types";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createBusinessRep($conn, $businessRep) {
    $query ="
        INSERT INTO business_reps (
            user_id,
            public_business_rep_id,
            first_name,
            last_name,
            middle_name,
            ext_name,
            birth_date,
            gender,
            user_address,
            active_phone_number,
            alternative_email,
            valid_id_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
    "isssssssssss", 
    $businessRep['user_id'], 
    $businessRep['public_id'], 
    $businessRep['first_name'], 
    $businessRep['last_name'], 
    $businessRep['middle_name'], 
    $businessRep['ext_name'], 
    $businessRep['birth_date'], 
    $businessRep['gender'], 
    $businessRep['user_address'], 
    $businessRep['active_phone_num'], 
    $businessRep['alternative_email'], 
    $businessRep['valid_id_url']
    );
    return $stmt->execute();
}

function createBusinessApplication($conn, $businessDetails) {
    $query ="
        INSERT INTO business_applications (
            public_business_application_id,
            business_rep_id,
            business_rep_position_id,
            business_name,
            business_desc,
            business_type_id,
            business_contact_num,
            business_email,  
            business_unit_number,
            business_street,
            business_city,
            business_province,
            business_postal_code,
            business_country,
            loc_lat,
            loc_long,
            business_permit_url,
            authorization_letter_url,
            agreed_to_terms,
            is_operating
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";     

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "siississssssssssssii",
        $businessDetails['public_business_application_id'],
        $businessDetails['business_rep_id'],
        $businessDetails['business_rep_position_id'],
        $businessDetails['business_name'],
        $businessDetails['business_desc'],
        $businessDetails['business_type_id'],
        $businessDetails['business_contact_num'],
        $businessDetails['business_email'],
        $businessDetails['business_unit_number'],
        $businessDetails['business_street'],
        $businessDetails['business_city'],
        $businessDetails['business_province'],
        $businessDetails['business_postal_code'],
        $businessDetails['business_country'],
        $businessDetails['loc_lat'],
        $businessDetails['loc_long'],
        $businessDetails['business_permit_url'],
        $businessDetails['authorization_letter_url'],
        $businessDetails['agreed_to_terms'],
        $businessDetails['is_operating']
    );
    return $stmt->execute();
}

function createBusinessPhotos($conn, $photo) {
    $query ="
        INSERT INTO business_photos (
            business_app_id,
            photo_url
        ) VALUES (?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "is",
        $photo['business_app_id'],
        $photo['photo_url']
    );
    return $stmt->execute();
}



?>