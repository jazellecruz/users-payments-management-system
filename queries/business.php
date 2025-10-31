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
            valid_id_url,
            profile_img_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
    "issssssssssss", 
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
    $businessRep['valid_id_url'],
    $businessRep['profile_img_url']
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

function createBusinessAppPhotos($conn, $photo) {
    $query ="
        INSERT INTO business_app_photos (
            business_app_id,
            photo_url,
            public_id
        ) VALUES (?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iss",
        $photo['business_app_id'],
        $photo['photo_url'],
        $photo['public_id']
    );
    return $stmt->execute();
}

function getBusinessApplicationsByRepId($conn, $businessRepId) {
    $query = "SELECT b_a.*, 
        b_t.business_type_name, 
        b_r.business_rep_code, 
        group_concat(b_p.photo_url) 
        FROM business_applications as b_a 
        LEFT JOIN business_types as b_t
        ON b_a.business_type_id = b_t.business_type_id 
        LEFT JOIN business_rep_positions AS b_r
        ON b_a.business_rep_position_id = b_r.business_rep_position_id
        LEFT JOIN business_app_photos as b_p
        ON b_a.business_application_id = b_p.business_app_id
        WHERE business_rep_id = ?
        GROUP BY business_application_id
        ORDER BY b_a.created_at DESC;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $businessRepId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAppsCountByStatus($conn, $businessRepId, $status) {
    $query = "SELECT COUNT(*) as count FROM business_applications WHERE business_rep_id = ?";
    if (!empty($status)) {
        $query .= " AND application_status = ?";
    }

    $stmt = $conn->prepare($query);

    if (!empty($status)) {
        $stmt->bind_param("is", $businessRepId, $status);
    } else {
        $stmt->bind_param("i", $businessRepId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'];
}

function getBusinessRepByUserId($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM business_reps WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateBusinessApplicationStatus($conn, $applicationId, $status, $remarks = null, $reviewedBy = null) {
    $query = "UPDATE business_applications 
    SET application_status = ?, 
    remarks = ?,
    reviewed_by = ?
    WHERE business_application_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $status, $remarks, $reviewedBy, $applicationId);

    return $stmt->execute();
}

function getApplicationById($conn, $applicationId) {
    $stmt = $conn->prepare("SELECT * FROM business_applications WHERE business_application_id = ?");
    $stmt->bind_param("i", $applicationId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getBusinessPhotosAppByAppId($conn, $applicationId) {
    $stmt = $conn->prepare("SELECT * FROM business_app_photos WHERE business_app_id = ?");
    $stmt->bind_param("i", $applicationId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createNewBusiness($conn, $businessData) {
    $query ="
        INSERT INTO businesses (
            public_business_id,
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
            is_operating,
            active_application_id 
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "siississsssssssssssii",
        $businessData['public_business_id'],
        $businessData['business_rep_id'],
        $businessData['business_rep_position_id'],
        $businessData['business_name'],
        $businessData['business_desc'],
        $businessData['business_type_id'],
        $businessData['business_contact_num'],
        $businessData['business_email'],
        $businessData['business_unit_number'],
        $businessData['business_street'],
        $businessData['business_city'],
        $businessData['business_province'],
        $businessData['business_postal_code'],
        $businessData['business_country'],
        $businessData['loc_lat'],
        $businessData['loc_long'],
        $businessData['business_permit_url'],
        $businessData['authorization_letter_url'],
        $businessData['agreed_to_terms'],
        $businessData['is_operating'],
        $businessData['active_application_id']
    );

    return $stmt->execute();
}

function createBusinessPhotos($conn, $photo) {
    $query ="
        INSERT INTO business_photos (
            business_id,
            photo_url,
            public_id
        ) VALUES (?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iss",
        $photo['business_id'],
        $photo['photo_url'],
        $photo['public_id']
    );
    return $stmt->execute();
}

function getBusinessesByRepId($conn, $businessRepId) {
    $query = "SELECT b.*, 
        b_t.business_type_name, 
        b_r.business_rep_code, 
        group_concat(b_p.photo_url) 
        FROM businesses as b 
        LEFT JOIN business_types as b_t
        ON b.business_type_id = b_t.business_type_id 
        LEFT JOIN business_rep_positions AS b_r
        ON b.business_rep_position_id = b_r.business_rep_position_id
        LEFT JOIN business_photos as b_p
        ON b.business_id = b_p.business_id
        WHERE b.business_rep_id = ?
        GROUP BY b.business_id
        ORDER BY b.created_at DESC;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $businessRepId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}   

function updateBusinessRepProfileImg($conn, $businessRepId, $imgUrl) {
    $query = "UPDATE business_reps 
    SET profile_img_url = ? 
    WHERE business_rep_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $imgUrl, $businessRepId);

    return $stmt->execute();
}

function updateBusinessRepInfo($conn, $repData) {
    $query = "UPDATE business_reps 
    SET first_name = ?, 
        last_name = ?, 
        middle_name = ?, 
        ext_name = ?, 
        birth_date = ?,
        gender = ?,
        user_address = ?,
        active_phone_number = ?,
        alternative_email = ?
    WHERE business_rep_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssssssssi",
        $repData['first_name'],
        $repData['last_name'],
        $repData['middle_name'],
        $repData['ext_name'],
        $repData['birth_date'],
        $repData['gender'],
        $repData['user_address'],
        $repData['active_phone_number'],
        $repData['alternative_email'],
        $repData['business_rep_id']
    );

    return $stmt->execute();
}

function getBusinessById($conn, $businessId) {
    $stmt = $conn->prepare("
        SELECT 
            b.*, 
            b_t.business_type_name, 
            b_rp.business_position_name, 
            b_a.application_status, 
            b_a.updated_at,
            b_a.created_at as application_created_at,
            b_a.public_business_application_id,
            b_a.application_status,
            group_concat(b_p.photo_url) 
        FROM businesses AS b 
        LEFT JOIN business_rep_positions AS b_rp
        ON b.business_rep_position_id = b_rp.business_rep_position_id
        LEFT JOIN business_types AS b_t
        ON b.business_type_id = b_t.business_type_id
        LEFT JOIN business_photos AS b_p
        ON b.business_id = b_p.business_id
        LEFT JOIN business_applications AS b_a
        ON b.active_application_id = b_a.business_application_id
        WHERE b.business_id = ?
        GROUP BY b.business_id;
    ");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getBusinessIdByPublicId($conn, $publicId) {
    $stmt = $conn->prepare("SELECT business_id FROM businesses WHERE public_business_id = ?;");
    $stmt->bind_param("s", $publicId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['business_id'];
}

function updateBusinessProfileImg($conn, $businessId, $imgUrl) {
    $query = "UPDATE businesses 
    SET business_profile_img = ? 
    WHERE business_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $imgUrl, $businessId);

    return $stmt->execute();
}

function updateBusinessCoverImg($conn, $businessId, $imgUrl) {
    $query = "UPDATE businesses 
    SET business_cover_img_url = ? 
    WHERE business_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $imgUrl, $businessId);

    return $stmt->execute();
}

function updateBusinessInfo($conn, $businessData) {
    $query = "UPDATE businesses 
    SET business_name = ?, 
        business_desc = ?, 
        business_type_id = ?, 
        business_contact_num = ?, 
        business_email = ?,  
        business_unit_number = ?, 
        business_street = ?, 
        business_city = ?, 
        business_province = ?, 
        business_postal_code = ?, 
        business_country = ?, 
        loc_lat = ?, 
        loc_long = ?, 
        is_operating = ?
    WHERE business_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssissssssssssii",
        $businessData['business_name'],
        $businessData['business_desc'],
        $businessData['business_type_id'],
        $businessData['business_contact_num'],
        $businessData['business_email'],
        $businessData['business_unit_number'],
        $businessData['business_street'],
        $businessData['business_city'],
        $businessData['business_province'],
        $businessData['business_postal_code'],
        $businessData['business_country'],
        $businessData['loc_lat'],
        $businessData['loc_long'],
        $businessData['is_operating'],
        $businessData['business_id']
    );

    return $stmt->execute();
}

function getBusinessPhotosByBusinessId($conn, $businessId) {
    $stmt = $conn->prepare("SELECT * FROM business_photos WHERE business_id = ?");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getBusinessPhotoById($conn, $photoId) {
    $stmt = $conn->prepare("SELECT * FROM business_photos WHERE business_photo_id = ?");
    $stmt->bind_param("i", $photoId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// always pass multiple ids as array and single id as integer
function deleteBusinessPhotosById($conn, $photoIds) {
    if(is_array($photoIds)) {
        $query = "DELETE FROM business_photos WHERE business_photo_id IN (" 
        . implode(',', array_map('intval', $photoIds)) 
        . ")";
        $stmt = $conn->prepare($query);
    } else {
        $query = "DELETE FROM business_photos WHERE business_photo_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $photoIds);
    }
    return $stmt->execute();
}

// always pass multiple ids as array and single id as integer
function getBusinessPhotosById($conn, $photoIds) {
    if(is_array($photoIds)) {
        $query = "SELECT * FROM business_photos WHERE business_photo_id IN (" 
        . implode(',', array_map('intval', $photoIds)) 
        . ")";
        $stmt = $conn->prepare($query);
    } else {
        $query = "SELECT * FROM business_photos WHERE business_photo_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $photoIds);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}



?>