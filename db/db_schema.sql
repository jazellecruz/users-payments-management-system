DROP DATABASE IF EXISTS user_payment_db;

CREATE DATABASE user_payment_db;

USE user_payment_db;

-- Role is stored in users table for simplicity.
-- If requirements change, roles would be moved 
-- to a separate table with a many-to-many 
-- relationship to users.
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255),
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    acc_img_url VARCHAR(255) DEFAULT NULL,
    role ENUM('basic', 'driver', 'admin', 'bus_rep') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);  

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    public_admin_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    ext_name VARCHAR(10),
    is_disabled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE oauth_accounts (
    oauth_acc_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,    
    oauth_user_id VARCHAR(255) UNIQUE NOT NULL,
    provider ENUM('google', 'facebook') NOT NULL,
    access_token VARCHAR(255),
    refresh_token VARCHAR(255),
    token_expiry TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

--  For simplicity, URL of documents are stored
-- in the driver_applications table. If requirements are to change
-- i.e. require implementing OCR and Document Verification with external
-- services, documents will be stored in a separate table.
CREATE TABLE driver_applications (
    driver_application_id INT AUTO_INCREMENT PRIMARY KEY,
    driver_app_public_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    ext_name VARCHAR(10),
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    user_address VARCHAR(255) NOT NULL,
    active_phone_number CHAR(11) NOT NULL, 
    alternative_email VARCHAR(100),
    license_number VARCHAR(50) NOT NULL,
    license_expiry_date DATE NOT NULL,
    license_img_url VARCHAR(255) NOT NULL,
    proof_of_address_img_url VARCHAR(255) NOT NULL,
    nbi_clearance_img_url VARCHAR(255) NOT NULL,
    id_pic_img_url VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP DEFAULT NULL,
    reviewed_by INT DEFAULT NULL,
    is_latest_approved BOOLEAN DEFAULT FALSE, -- is_latest_approved becomes true when application is approved
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (reviewed_by) REFERENCES admins(admin_id)
);

-- "Live" profile of drivers 
CREATE TABLE drivers (
    driver_id INT AUTO_INCREMENT PRIMARY KEY,
    public_driver_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    ext_name VARCHAR(10),
    birth_date DATE NOT NULL,
    user_address VARCHAR(255) NOT NULL,
    active_phone_number CHAR(11) NOT NULL, 
    alternative_email VARCHAR(100),
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_disabled BOOLEAN DEFAULT FALSE,
    disabled_at TIMESTAMP DEFAULT NULL,
    disabled_by INT DEFAULT NULL,
    active_application_id INT NOT NULL, -- the latest approved application to reference approved documents
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (active_application_id) REFERENCES driver_applications(driver_application_id),
    FOREIGN KEY (disabled_by) REFERENCES admins(admin_id)
);

CREATE TABLE business_types (
    business_type_id INT AUTO_INCREMENT PRIMARY KEY,
    business_type_code VARCHAR(50) UNIQUE NOT NULL,
    business_type_name VARCHAR(100) NOT NULL,         
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE business_rep_positions (
    business_rep_position_id INT AUTO_INCREMENT PRIMARY KEY,
    business_rep_code VARCHAR(50) UNIQUE NOT NULL,
    business_position_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE business_reps (
    business_rep_id INT AUTO_INCREMENT PRIMARY KEY,
    public_business_rep_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    ext_name VARCHAR(10),
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    user_address VARCHAR(255) NOT NULL,
    active_phone_number CHAR(11) NOT NULL, 
    alternative_email VARCHAR(100),
    valid_id_url VARCHAR(255) NOT NULL,
    is_disabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    disabled_at TIMESTAMP DEFAULT NULL,
    disabled_by INT DEFAULT NULL,
    FOREIGN KEY (disabled_by) REFERENCES admins(admin_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Roles of representatives are stored in business_reps table for simplicity.
-- Enforces one representative per business profile.
CREATE TABLE business_applications (
    business_application_id INT AUTO_INCREMENT PRIMARY KEY,
    public_business_application_id VARCHAR(50) NOT NULL UNIQUE,
    business_rep_id INT NOT NULL,
    business_rep_position_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_desc TEXT NOT NULL,
    business_type_id INT NOT NULL,
    business_contact_num VARCHAR(15) NOT NULL,
    business_email VARCHAR(100) NOT NULL,
    business_unit_number VARCHAR(50),
    business_street VARCHAR(100) NOT NULL,
    business_postal_code CHAR(10) NOT NULL,
    business_city VARCHAR(100) NOT NULL,
    business_province VARCHAR(100) NOT NULL,
    business_country VARCHAR(100) NOT NULL,
    loc_lat DECIMAL(10, 8) NOT NULL,
    loc_long DECIMAL(11, 8) NOT NULL,
    application_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    business_permit_url VARCHAR(255) NOT NULL,
    authorization_letter_url VARCHAR(255), -- nullable for business owners
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    is_operating BOOLEAN DEFAULT FALSE,
    is_latest_approved BOOLEAN DEFAULT FALSE, 
    reviewed_at TIMESTAMP DEFAULT NULL,
    reviewed_by INT DEFAULT NULL,
    FOREIGN KEY (business_rep_id) REFERENCES business_reps(business_rep_id),
    FOREIGN KEY (business_type_id) REFERENCES business_types(business_type_id),
    FOREIGN KEY (business_rep_position_id) REFERENCES business_rep_positions(business_rep_position_id)
);

-- Longitude and Latitude are stored as DECIMAL instead of POINT 
-- as it is much more straightforward to work with in application code.
CREATE TABLE businesses (
    business_id INT AUTO_INCREMENT PRIMARY KEY,
    public_business_id VARCHAR(50) NOT NULL UNIQUE,
    business_rep_id INT NOT NULL,
    business_rep_role_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_desc TEXT NOT NULL,
    business_type_id INT NOT NULL,
    business_unit_number VARCHAR(50),
    business_street VARCHAR(100) NOT NULL,
    business_postal_code CHAR(10) NOT NULL,
    business_city VARCHAR(100) NOT NULL,
    business_province VARCHAR(100) NOT NULL,
    business_country VARCHAR(100) NOT NULL,
    loc_lat DECIMAL(10, 8) NOT NULL, 
    loc_long DECIMAL(11, 8) NOT NULL,
    is_operating BOOLEAN DEFAULT FALSE,
    business_profile_img VARCHAR(255) DEFAULT NULL,
    active_application_id INT NOT NULL,
    is_disabled BOOLEAN DEFAULT FALSE,
    disabled_at TIMESTAMP DEFAULT NULL, 
    disabled_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_rep_id) REFERENCES business_reps(business_rep_id),
    FOREIGN KEY (business_rep_role_id) REFERENCES business_rep_positions(business_rep_position_id),
    FOREIGN KEY (business_type_id) REFERENCES business_types(business_type_id),
    FOREIGN KEY (active_application_id) REFERENCES business_applications(business_application_id),
    FOREIGN KEY (disabled_by) REFERENCES admins(admin_id)
);

CREATE TABLE business_photos (
    business_photo_id INT AUTO_INCREMENT PRIMARY KEY,
    business_app_id INT NOT NULL,
    photo_url VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_app_id) REFERENCES business_applications(business_application_id)
);