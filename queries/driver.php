<?php 

function createDriverApplication($conn, $appDetails) {
    $query ="
        INSERT INTO driver_applications (
            user_id,
            driver_app_public_id,
            first_name,
            last_name,
            middle_name,
            ext_name,
            alternative_email,
            active_phone_number,
            user_address,
            birth_date,
            gender,
            license_number,
            license_expiry_date,
            license_img_url,
            proof_of_address_img_url,
            nbi_clearance_img_url,
            id_pic_img_url,
            agreed_to_terms
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "isssssssssssssssss", 
        $appDetails['user_id'], 
        $appDetails['driver_app_public_id'], 
        $appDetails['first_name'], 
        $appDetails['last_name'], 
        $appDetails['middle_name'], 
        $appDetails['ext_name'], 
        $appDetails['alt_email'], 
        $appDetails['contact_number'], 
        $appDetails['address'], 
        $appDetails['birthdate'], 
        $appDetails['gender'], 
        $appDetails['license_number'], 
        $appDetails['license_expiry'], 
        $appDetails['license_img_url'], 
        $appDetails['proof_of_address_img_url'], 
        $appDetails['nbi_clearance_img_url'], 
        $appDetails['id_picture_img_url'],
        $appDetails['agreed_to_terms'], 
    );
    return $stmt->execute();
}

?>