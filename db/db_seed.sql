USE user_payment_db;

DELETE FROM business_types;
DELETE FROM business_rep_positions;
INSERT INTO business_types (business_type_code, business_type_name) VALUES
('CAFE', 'Cafe'),
('RESTAURANT', 'Restaurant'),
('HOTEL', 'Hotel'),
('RESORT', 'Resort'),
('HOMESTAY', 'Homestay / Airbnb'),
('PARK', 'Park / Plaza'),
('MUSEUM', 'Museum'),
('NATURE', 'Nature Attraction'),
('HISTORICAL', 'Historical Landmark'),
('CHURCH', 'Church / Religious Site'),
('MARKET', 'Public Market / Souvenir Shop'),
('MALL', 'Shopping Mall'),
('BAR', 'Bar / Nightlife'),
('THEME_PARK', 'Theme Park'),
('WATERFALL', 'Waterfall'),
('ZOO', 'Zoo / Wildlife Sanctuary'),
('CULTURAL', 'Cultural Center / Heritage Village'),
('SPA', 'Spa / Wellness'),
('TRANSPORT', 'Transport Hub (e.g., Airport, Bus Terminal)'),
('EDUCATIONAL', 'Educational Site (e.g., Library, University)'),
('SPORTS', 'Sports Facility / Gym'),
('ENTERTAINMENT', 'Entertainment Venue'),
('HEALTHCARE', 'Healthcare Facility (e.g., Clinic, Hospital)'),
('GOVERNMENT', 'Government Building / Embassy'),
('TECHNOLOGY', 'Tech Hub / Innovation Center'),
('CO_WORKING', 'Co-working Space'),
('AGRICULTURAL', 'Farm / Agricultural Site'),
('INDUSTRIAL', 'Industrial Site / Factory Tour'),
('FESTIVAL', 'Festival / Events'),
('TOUR_OPERATOR', 'Tour Operator / Travel Agency'),
('OTHER_LODGING', 'Other Lodging Establishments'),
('OTHER_FOOD_BEVERAGE', 'Other Food & Beverage Establishments'),
('OTHERS', 'Others');

INSERT INTO business_rep_positions (business_rep_code, business_position_name) 
VALUES
    ('OWNER', 'Owner'),
    ('MANAGER', 'Manager'),
    ('AUTHORIZED_REP', 'Authorized Representative'),
    ('CO_OWNER', 'Co-owner / Partner'),
    ('SUPERVISOR', 'Supervisor'),
    ('STAFF_IN_CHARGE', 'Staff-in-Charge'),
    ('ADMIN', 'Administrator');

INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Jane', 'Doe', 'cruz.jazelle.lapira@gmail.com', '$2y$10$Hwmnty3GFn/ZhdmaVWSpyuiJqnm3KSqoHtHa4EWNNRsxEFMmHRiK6', 'basic'),
('John', 'Doe', 'cruz.jazelle.lapira@gmail.com', '$2y$10$Hwmnty3GFn/ZhdmaVWSpyuiJqnm3KSqoHtHa4EWNNRsxEFMmHRiK6', 'driver');

INSERT INTO platform_accounts (
    public_platform_account_id,
    platform_account_name,
    platform_account_email,
    platform_account_type,
    external_account_id,
    owner_type,
    owner_id
) VALUES (
    'ACC-MA-CBYGKH9DVLW1',
    'Journeolink Platform',
    'cruz.jazelle.lapira@gmail.com',
    'master_account',
    '68c3f7135d83025f14eda7fa',
    'platform',
    null
);