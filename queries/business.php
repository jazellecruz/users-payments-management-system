<?php 

/**
 * Debug function to show the prepared query with actual values
 */
function debugPreparedStatement($query, $types, $params) {
    echo "<h3>Debug Prepared Statement:</h3>";
    echo "<strong>Original Query:</strong><br>";
    echo "<pre>" . htmlspecialchars($query) . "</pre><br>";
    
    echo "<strong>Parameter Types:</strong> " . $types . "<br>";
    echo "<strong>Parameters:</strong><br>";
    echo "<pre>" . print_r($params, true) . "</pre>";
    
    // Simulate the final query (approximate)
    $debugQuery = $query;
    $paramIndex = 0;
    
    // Replace ? with actual values for visualization
    $finalQuery = preg_replace_callback('/\?/', function($matches) use ($params, &$paramIndex) {
        if (isset($params[$paramIndex])) {
            $value = $params[$paramIndex];
            $paramIndex++;
            
            // Add quotes for strings, keep numbers as-is
            if (is_string($value)) {
                return "'" . addslashes($value) . "'";
            } elseif (is_null($value)) {
                return "NULL";
            } else {
                return $value;
            }
        }
        return '?';
    }, $debugQuery);
    
    echo "<strong>Approximate Final Query:</strong><br>";
    echo "<pre>" . htmlspecialchars($finalQuery) . "</pre>";
    echo "<hr>";
}

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
        LEFT JOIN business_photos as b_p
        ON b_a.business_application_id = b_p.business_app_id
        WHERE business_rep_id = ?
        GROUP BY business_application_id;";
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

?>