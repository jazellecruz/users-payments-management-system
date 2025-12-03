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
    phone_number CHAR(11) DEFAULT NULL,
    acc_img_url VARCHAR(255) DEFAULT NULL,
    role ENUM('basic', 'driver', 'admin', 'bus_rep') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    disabled_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE oauth_accounts (
    oauth_acc_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,    
    oauth_user_id VARCHAR(255) UNIQUE NOT NULL,
    provider ENUM('google', 'facebook') NOT NULL,
    access_token TEXT,
    refresh_token TEXT,
    token_expiry DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
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
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME DEFAULT NULL,
    reviewed_by INT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    remarks TEXT DEFAULT NULL,
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
    license_number VARCHAR(50) NOT NULL,
    license_expiry_date DATE NOT NULL,
    license_img_url VARCHAR(255) NOT NULL,
    proof_of_address_img_url VARCHAR(255) NOT NULL,
    nbi_clearance_img_url VARCHAR(255) NOT NULL,
    id_pic_img_url VARCHAR(255) NOT NULL,
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_disabled BOOLEAN DEFAULT FALSE,
    disabled_at DATETIME DEFAULT NULL,
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
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE business_rep_positions (
    business_rep_position_id INT AUTO_INCREMENT PRIMARY KEY,
    business_rep_code VARCHAR(50) UNIQUE NOT NULL,
    business_position_name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
    profile_img_url VARCHAR(255) DEFAULT NULL,
    is_disabled BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    disabled_at DATETIME DEFAULT NULL,
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
    application_status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    business_permit_url VARCHAR(255) NOT NULL,
    authorization_letter_url VARCHAR(255), -- nullable for business owners
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    is_operating BOOLEAN,
    is_latest_approved BOOLEAN DEFAULT FALSE, 
    reviewed_at DATETIME DEFAULT NULL,
    reviewed_by INT DEFAULT NULL,
    remarks TEXT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by) REFERENCES admins(admin_id),
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
    is_operating BOOLEAN DEFAULT FALSE,
    business_profile_img TEXT DEFAULT NULL,
    business_cover_img_url TEXT DEFAULT NULL,
    business_permit_url VARCHAR(255) NOT NULL,
    authorization_letter_url VARCHAR(255), -- nullable for business owners
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    active_application_id INT NOT NULL,
    is_disabled BOOLEAN DEFAULT FALSE,
    disabled_at DATETIME DEFAULT NULL, 
    disabled_by INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_rep_id) REFERENCES business_reps(business_rep_id),
    FOREIGN KEY (business_rep_position_id) REFERENCES business_rep_positions(business_rep_position_id),
    FOREIGN KEY (business_type_id) REFERENCES business_types(business_type_id),
    FOREIGN KEY (active_application_id) REFERENCES business_applications(business_application_id),
    FOREIGN KEY (disabled_by) REFERENCES admins(admin_id)
);

CREATE TABLE business_photos (
    business_photo_id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    public_id VARCHAR(100),
    photo_url VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id)
);

CREATE TABLE business_app_photos (
    business_photo_id INT AUTO_INCREMENT PRIMARY KEY,
    business_app_id INT NOT NULL,
    public_id VARCHAR(100),
    photo_url VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_app_id) REFERENCES business_applications(business_application_id)
);

CREATE TABLE unverified_users (
	unverified_user_id INT AUTO_INCREMENT PRIMARY KEY,
    verification_code VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255),
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('basic', 'driver', 'admin', 'bus_rep') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- RIDE BOOKINGS TABLE (THIS IS A TEMP SCHEMA, NOT THE ACTUAL SCHEMA)
-- =========================================
CREATE TABLE ride_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    public_booking_id VARCHAR(100) UNIQUE,
    booked_by INT, -- customer
    driver_id INT, -- assigned driver
    pickup_location VARCHAR(255),
    dropoff_location VARCHAR(255),
    ride_status ENUM('pending', 'accepted', 'completed', 'cancelled') DEFAULT 'pending',
    total_fare DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booked_by) REFERENCES users(user_id),
    FOREIGN KEY (driver_id) REFERENCES users(user_id)
);

-- =========================================
-- CAR RENTALS TABLE (THIS IS A TEMP SCHEMA, NOT THE ACTUAL SCHEMA)
-- =========================================
CREATE TABLE car_rentals (
    rental_id INT AUTO_INCREMENT PRIMARY KEY,
    public_rental_id VARCHAR(100),
    user_id INT NOT NULL, -- customer
    driver_id INT DEFAULT NULL, -- optional driver
    car_model VARCHAR(100),
    rent_start_date DATETIME,
    rent_end_date DATETIME,
    rental_status ENUM('pending', 'approved', 'ongoing', 'completed', 'cancelled') DEFAULT 'pending',
    total_rental_cost DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (driver_id) REFERENCES users(user_id)
);

-- =========================================
-- PLATFORM ACCOUNTS (Xendit accounts)
-- external_account_id refers to Xendit Sub Account ID
-- =========================================
CREATE TABLE platform_accounts (
    platform_account_id INT AUTO_INCREMENT PRIMARY KEY,
    public_platform_account_id VARCHAR(100) UNIQUE,
    platform_account_name VARCHAR(150) NOT NULL,
    platform_account_email VARCHAR(100) NOT NULL,
    platform_account_type ENUM('sub_account', 'master_account') NOT NULL,
    external_account_id VARCHAR(150) NOT NULL,
    owner_type ENUM('driver', 'platform') NOT NULL,
    owner_id INT DEFAULT NULL,
    is_disabled BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
);

-- =========================================
-- EXTERNAL WALLETS (GCash / PayMaya)
-- external_account_number refers to GCash / PayMaya account number
-- external_account_name refers to GCash / PayMaya account name
-- =========================================
CREATE TABLE external_wallets (
    external_wallet_id INT AUTO_INCREMENT PRIMARY KEY,
    public_external_wallet_id VARCHAR(100) UNIQUE,
    owner_type ENUM('driver', 'basic', 'platform') NOT NULL,
    owner_id INT NOT NULL,
    external_wallet_number VARCHAR(100) NOT NULL,
    external_wallet_name VARCHAR(150) NOT NULL,
    external_wallet_type ENUM('g_cash', 'paymaya') NOT NULL,
    is_disabled BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id)
);

-- =========================================
-- TRANSACTIONS
-- source_account_id refers to the account where the money is coming from (can be platform acc or external wallet) 
-- destination_account_id refers to the account where the money is going to (can be platform acc or external wallet) 
-- external_transaction_id refers to the transaction ID from external payment gateway (Xendit)
-- If transaction is cash, a transaction row will be created after the service has been completed
-- =========================================
CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    public_transaction_id VARCHAR(100) UNIQUE,
    paid_by INT,
    paid_to INT,
    source_account_type ENUM('external_wallet', 'platform_account'),
    destination_account_type ENUM('external_wallet', 'platform_account'),
    source_account_id INT,
    destination_account_id INT,
    transaction_method ENUM('e_wallet', 'cash', 'card', 'xendit_internal_transfer') NOT NULL,
    transaction_channel ENUM('g_cash', 'paymaya', 'cash', 'xendit_internal_transfer'),
    external_transaction_id VARCHAR(150),
    provider_transaction_ref_id VARCHAR(150),
    transaction_status ENUM('pending', 'cancelled', 'paid', 'rejected', 'refunded', 'partially_paid', 'collected', 'processing_settlement', 'voided') DEFAULT 'pending',
    settlement_status ENUM('pending', 'settled', 'failed') DEFAULT 'pending',
    total_amount_paid DECIMAL(10,2), -- the total amount involved in the transaction
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paid_by) REFERENCES users(user_id),
    FOREIGN KEY (paid_to) REFERENCES users(user_id),
    FOREIGN KEY (source_account_id) REFERENCES external_wallets(external_wallet_id),
    FOREIGN KEY (destination_account_id) REFERENCES platform_accounts(platform_account_id)
);

-- =========================================
-- PAYMENTS
-- payments refers to the request or intent of payment for a specific service
-- service_id can be 'ride_booking' or 'car_rental' entity
-- paid_by refers to the user who made the payment
-- =========================================
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    public_payment_id VARCHAR(100) UNIQUE,
    service_type ENUM('ride_booking', 'car_rental') NOT NULL,
    service_id INT NOT NULL,
    payment_type ENUM('full_payment', 'down_payment', 'balance_payment') NOT NULL,
    payment_method ENUM('e_wallet', 'cash', 'card') NOT NULL,
    payment_channel ENUM('g_cash', 'paymaya', 'cash'),
    paid_by INT NOT NULL,
    transaction_id INT NULL,
    total_amount DECIMAL(10,2),
    platform_fee DECIMAL(10,2),
    net_pay DECIMAL(10,2),
    tax_pay DECIMAL(10,2),
    payment_status ENUM('pending', 'cancelled', 'paid', 'rejected', 'refunded', 'partially_paid', 'collected', 'processing_settlement', 'voided') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paid_by) REFERENCES users(user_id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id)
);

-- =========================================
-- PAYOUTS
-- =========================================
CREATE TABLE payouts (
    payout_id INT AUTO_INCREMENT PRIMARY KEY,
    public_payout_id VARCHAR(100) UNIQUE,
    service_type ENUM('ride_booking', 'car_rental') NOT NULL,
    service_id INT NOT NULL,
    paid_to INT NOT NULL,
    total_payout_amount DECIMAL(10,2),
    transaction_id INT NULL,
    source_account_id INT NOT NULL,
    destination_account_id INT NOT NULL,
    payment_id INT NOT NULL,
    payout_status ENUM('pending', 'cancelled', 'paid', 'rejected', 'refunded', 'partially_paid', 'processing_settlement', 'voided') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paid_to) REFERENCES users(user_id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id),
    FOREIGN KEY (source_account_id) REFERENCES platform_accounts(platform_account_id),
    FOREIGN KEY (destination_account_id) REFERENCES platform_accounts(platform_account_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

-- =========================================
-- WITHDRAWALS
-- =========================================
CREATE TABLE withdrawals (
    withdrawal_id INT AUTO_INCREMENT PRIMARY KEY,
    public_withdrawal_id VARCHAR(100) UNIQUE,
    withdrew_by INT NOT NULL,
    transaction_id INT NULL,
    user_type ENUM('driver', 'platform') NOT NULL,
    total_deducted_amount DECIMAL(10,2),
    transfer_fee DECIMAL(10,2),
    withdrawal_amount DECIMAL(10,2),
    source_account_id INT NOT NULL,
    destination_account_id INT NOT NULL,
    withdrawal_status ENUM('pending', 'cancelled', 'paid', 'rejected', 'refunded', 'processing_settlement', 'voided') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (withdrew_by) REFERENCES users(user_id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id),
    FOREIGN KEY (source_account_id) REFERENCES platform_accounts(platform_account_id),
    FOREIGN KEY (destination_account_id) REFERENCES external_wallets(external_wallet_id)
);

-- =========================================
-- PAYMENT SESSIONS
-- =========================================
CREATE TABLE payment_sessions (
    payment_session_id INT AUTO_INCREMENT PRIMARY KEY,
    public_payment_session_id VARCHAR(255) NOT NULL,
    transaction_id INT, -- internal transaction ID (can be null if transaction is cash)
    payment_id INT, -- payment entity
    service_type ENUM('ride_booking', 'car_rental') NOT NULL,
    service_id INT NOT NULL, -- internal ID of service
    payment_session_status ENUM('pending', 'cancelled', 'paid', 'rejected', 'refunded', 'partially_paid', 'collected', 'processing_settlement', 'voided') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);


-- =========================================
-- INVOICES
-- Invoices are for client/customer payments  
-- invoice_total_amount is the total service cost expected to be paid
-- invoice_amount_paid is the cumulative payment of user
-- =========================================
CREATE TABLE invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    public_invoice_id VARCHAR(100) UNIQUE,
    service_type ENUM('ride_booking', 'car_rental') NOT NULL,
    service_id INT NOT NULL,
    invoice_status ENUM('pending', 'cancelled', 'paid', 'rejected', 'refunded', 'partially_paid', 'collected') DEFAULT 'pending',
    invoice_total_amount DECIMAL(10,2),
    invoice_amount_paid DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================
-- INVOICE PAYMENTS
-- Joint table for linking payments to an invoice, an invoice can have mulitple payments (car-rental has down payment + full payment)
-- =========================================
CREATE TABLE invoice_payments (
    invoice_payment_id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    payment_type ENUM('full_payment', 'down_payment', 'balance_payment'),
    payment_id INT NOT NULL,
    amount_paid DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

-- =========================================
-- LEDGER ENTRIES (single source of truth)
-- =========================================
CREATE TABLE ledger_entries (
    ledger_entry_id INT AUTO_INCREMENT PRIMARY KEY,
    public_entry_id VARCHAR(100) UNIQUE,
    entry_type ENUM('debit', 'credit') NOT NULL,
    transaction_id INT NOT NULL,
    transaction_type ENUM('payment', 'payout', 'withdrawal', 'refund'),
    payment_type ENUM('full_payment','down_payment','balance_payment'),
    entry_category ENUM('tax_pay','platform_fee','net_pay','total_amount','transfer_fee','withdrawal_amount', 'payout_amount', 'refund_amount'),
    source_type ENUM('e_wallet', 'cash', 'platform_account'),
    payment_id INT NULL,
    withdrawal_id INT NULL,
    payout_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id),
    FOREIGN KEY (withdrawal_id) REFERENCES withdrawals(withdrawal_id),
    FOREIGN KEY (payout_id) REFERENCES payouts(payout_id)
);

CREATE TABLE platform_accounts_balances (
    platform_account_balance_id INT PRIMARY KEY AUTO_INCREMENT,
    platform_account_id INT,
    current_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (platform_account_id) REFERENCES platform_accounts(platform_account_id)
);

CREATE TABLE platform_revenue (
    platform_revenue_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    payment_id INT NULL,
    service_id INT NULL,
    service_type ENUM('ride_booking', 'car_rental'),
    platform_fee DECIMAL(10,2) NOT NULL,
    tax_collected DECIMAL(10,2) DEFAULT 0,
    total_revenue DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

CREATE TABLE pending_account_deductions (
	pending_account_deduction_id INT AUTO_INCREMENT PRIMARY KEY,
    platform_account_id INT,
    user_id INT,
    amount_to_deduct DECIMAL(10,2),
    deduction_for ENUM('platform_fee', 'tax_pay'),
    deduction_status ENUM('pending', 'collected', 'transferred'),
	collected_at DATETIME DEFAULT NULL,
    transferred_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (platform_account_id) REFERENCES platform_accounts(platform_account_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE driver_earnings (
	driver_earning_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    payment_id INT, 
    service_type ENUM('ride_booking', 'car_rental'),
    service_id INT,
    amount_earned DECIMAL(10, 2),
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);