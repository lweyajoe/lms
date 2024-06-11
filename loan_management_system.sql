USE mysql;
DROP DATABASE IF EXISTS `loan_management_system`;
CREATE DATABASE `loan_management_system`
    CHARACTER SET  utf8mb4
    COLLATE utf8mb4_general_ci;
USE `loan_management_system`;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    file_no VARCHAR(10) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'client') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE call_back_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE managers (
    manager_id VARCHAR(10) PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    county VARCHAR(100) NOT NULL,
    town_centre VARCHAR(100) NOT NULL,
    national_id VARCHAR(20) NOT NULL UNIQUE,
    id_photo_front VARCHAR(255) NOT NULL,
    id_photo_back VARCHAR(255) NOT NULL,
    nssf VARCHAR(20) NOT NULL,
    nhif VARCHAR(20) NOT NULL,
    kra_pin VARCHAR(20) NOT NULL UNIQUE,
    date_of_onboarding DATE NOT NULL,
    residence_nearest_building VARCHAR(255) NOT NULL,
    residence_nearest_road VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    next_of_kin_name VARCHAR(255) NOT NULL,
    next_of_kin_phone_number VARCHAR(20) NOT NULL,
    next_of_kin_relation VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE clients (
    client_id VARCHAR(10) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    county VARCHAR(50) NOT NULL,
    town_centre VARCHAR(100) NOT NULL,
    national_id VARCHAR(20) NOT NULL UNIQUE,
    id_photo_front VARCHAR(255) NOT NULL,
    id_photo_back VARCHAR(255) NOT NULL,
    work_economic_activity VARCHAR(100) NOT NULL,
    residence_nearest_building VARCHAR(100) NOT NULL,
    residence_nearest_road VARCHAR(100) NOT NULL,
    date_of_onboarding DATE NOT NULL,
    onboarding_officer VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    next_of_kin_name VARCHAR(100) NOT NULL,
    next_of_kin_phone_number VARCHAR(20) NOT NULL,
    next_of_kin_relation VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE loan_applications (
    loan_id VARCHAR(10) PRIMARY KEY,
    client_id VARCHAR(10) NOT NULL,
    national_id VARCHAR(20) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    requested_amount DECIMAL(10, 2) NOT NULL,
    loan_purpose TEXT NOT NULL,
    duration INT NOT NULL,
    duration_period ENUM('Week', 'Month', 'Year') NOT NULL,
    date_applied DATE NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    interest_rate_period ENUM('Week', 'Month', 'Year') NOT NULL,
    collateral_name VARCHAR(100) NOT NULL,
    collateral_value DECIMAL(10, 2) NOT NULL,
    collateral_pic1 VARCHAR(255) NOT NULL,
    collateral_pic2 VARCHAR(255) NOT NULL,
    guarantor1_name VARCHAR(100) NOT NULL,
    guarantor1_phone VARCHAR(20) NOT NULL,
    guarantor2_name VARCHAR(100) NOT NULL,
    guarantor2_phone VARCHAR(20) NOT NULL,
    loan_status ENUM('Pending', 'Active', 'Rejected', 'Cleared', 'Defaulted') DEFAULT 'Pending' NOT NULL,
    onboarding_officer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id)
    );

CREATE TABLE active_loans (
    loan_id VARCHAR(10) PRIMARY KEY,
    client_id VARCHAR(10) NOT NULL,
    national_id VARCHAR(20) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    requested_amount DECIMAL(10, 2) NOT NULL,
    loan_purpose TEXT NOT NULL,
    duration INT NOT NULL,
    duration_period ENUM('Week', 'Month', 'Year') NOT NULL,
    date_applied DATE NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    interest_rate_period ENUM('Week', 'Month', 'Year') NOT NULL,
    collateral_name VARCHAR(100) NOT NULL,
    collateral_value DECIMAL(10, 2) NOT NULL,
    collateral_pic1 VARCHAR(255) NOT NULL,
    collateral_pic2 VARCHAR(255) NOT NULL,
    guarantor1_name VARCHAR(100) NOT NULL,
    guarantor1_phone VARCHAR(20) NOT NULL,
    guarantor2_name VARCHAR(100) NOT NULL,
    guarantor2_phone VARCHAR(20) NOT NULL,
    loan_status ENUM('Pending', 'Active', 'Rejected', 'Cleared', 'Defaulted') NOT NULL,
    onboarding_officer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id)
    );

CREATE TABLE loan_info (
    loan_id VARCHAR(10) PRIMARY KEY,
    client_id VARCHAR(10) NOT NULL,
    national_id VARCHAR(20) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    requested_amount DECIMAL(10, 2) NOT NULL,
    loan_purpose TEXT NOT NULL,
    duration INT NOT NULL,
    duration_period ENUM('Week', 'Month', 'Year') NOT NULL,
    date_applied DATE NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    interest_rate_period ENUM('Week', 'Month', 'Year') NOT NULL,
    collateral_name VARCHAR(100) NOT NULL,
    collateral_value DECIMAL(10, 2) NOT NULL,
    collateral_pic1 VARCHAR(255) NOT NULL,
    collateral_pic2 VARCHAR(255) NOT NULL,
    guarantor1_name VARCHAR(100) NOT NULL,
    guarantor1_phone VARCHAR(20) NOT NULL,
    guarantor2_name VARCHAR(100) NOT NULL,
    guarantor2_phone VARCHAR(20) NOT NULL,
    loan_status ENUM('Pending', 'Active', 'Rejected', 'Cleared', 'Defaulted') NOT NULL,
    onboarding_officer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_change TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id)
    );

CREATE TABLE rejected_loans (
    loan_id VARCHAR(10) PRIMARY KEY,
    client_id VARCHAR(10) NOT NULL,
    national_id VARCHAR(20) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    requested_amount DECIMAL(10, 2) NOT NULL,
    loan_purpose TEXT NOT NULL,
    duration INT NOT NULL,
    duration_period ENUM('Week', 'Month', 'Year') NOT NULL,
    date_applied DATE NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    interest_rate_period ENUM('Week', 'Month', 'Year') NOT NULL,
    collateral_name VARCHAR(100) NOT NULL,
    collateral_value DECIMAL(10, 2) NOT NULL,
    collateral_pic1 VARCHAR(255) NOT NULL,
    collateral_pic2 VARCHAR(255) NOT NULL,
    guarantor1_name VARCHAR(100) NOT NULL,
    guarantor1_phone VARCHAR(20) NOT NULL,
    guarantor2_name VARCHAR(100) NOT NULL,
    guarantor2_phone VARCHAR(20) NOT NULL,
    loan_status ENUM('Pending', 'Active', 'Rejected', 'Cleared', 'Defaulted') NOT NULL,
    onboarding_officer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id)
    );

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id VARCHAR(10) NOT NULL,
    national_id VARCHAR(20) NOT NULL,
    transaction_reference VARCHAR(50) NOT NULL,
    payment_mode VARCHAR(50) NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (loan_id) REFERENCES loan_info(loan_id)
    );

CREATE TABLE loan_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id VARCHAR(10) NOT NULL,
    national_id VARCHAR(50) NOT NULL,
    collateral_status VARCHAR(50) NOT NULL,
    loan_status ENUM('Pending', 'Active', 'Rejected', 'Cleared', 'Defaulted') NOT NULL,
    FOREIGN KEY (loan_id) REFERENCES loan_info(loan_id)
    );

CREATE TABLE loan_id_tracker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_id VARCHAR(10) NOT NULL
    );

CREATE TABLE homepage_navbar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    hours VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    facebook_link VARCHAR(255) NOT NULL,
    twitter_link VARCHAR(255) NOT NULL,
    linkedin_link VARCHAR(255) NOT NULL
    );

CREATE TABLE homepage_carousel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    caption_title VARCHAR(255) NOT NULL,
    caption_text VARCHAR(255) NOT NULL,
    button_text VARCHAR(50) NOT NULL,
    button_link VARCHAR(255) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT 0
    );

CREATE TABLE homepage_about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    about_title VARCHAR(255) NOT NULL,
    about_text VARCHAR(255) NOT NULL,
    story_text TEXT NOT NULL,
    mission_text TEXT NOT NULL,
    vision_text TEXT NOT NULL,
    feature1_title VARCHAR(255) NOT NULL,
    feature1_text VARCHAR(255) NOT NULL,
    feature1_icon VARCHAR(50) NOT NULL,
    feature2_title VARCHAR(255) NOT NULL,
    feature2_text VARCHAR(255) NOT NULL,
    feature2_icon VARCHAR(50) NOT NULL,
    feature3_title VARCHAR(255) NOT NULL,
    feature3_text VARCHAR(255) NOT NULL,
    feature3_icon VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) NOT NULL
    );

CREATE TABLE homepage_service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    main_title VARCHAR(255) NOT NULL,
    main_subtitle VARCHAR(255) NOT NULL,
    service1_title VARCHAR(255) NOT NULL,
    service1_text TEXT NOT NULL,
    service1_image VARCHAR(255) NOT NULL,
    service2_title VARCHAR(255) NOT NULL,
    service2_text TEXT NOT NULL,
    service2_image VARCHAR(255) NOT NULL,
    service3_title VARCHAR(255) NOT NULL,
    service3_text TEXT NOT NULL,
    service3_image VARCHAR(255) NOT NULL,
    service4_title VARCHAR(255) NOT NULL,
    service4_text TEXT NOT NULL,
    service4_image VARCHAR(255) NOT NULL
    );

CREATE TABLE homepage_team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255)
    );

CREATE TABLE homepage_footer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100),
    twitter VARCHAR(255),
    facebook VARCHAR(255),
    youtube VARCHAR(255),
    linkedin VARCHAR(255),
    services TEXT,
    quick_links TEXT
    );

    

INSERT INTO loan_id_tracker (last_id) VALUES ('ln000000');


--- auto-generate loan ids
DELIMITER //

CREATE TRIGGER generate_loan_id
BEFORE INSERT ON loan_applications
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    
    -- Retrieve the last generated loan_id
    SET next_id = (SELECT last_id FROM loan_id_tracker FOR UPDATE) + 1;
    
    -- Update the tracker with the new loan_id
    UPDATE loan_id_tracker SET last_id = next_id;
    
    -- Set the new loan_id in the desired format
    SET NEW.loan_id = CONCAT('ln', LPAD(next_id, 6, '0'));
END;
//

DELIMITER ;


--- auto-generate client ids
DELIMITER //

CREATE TRIGGER generate_client_id
BEFORE INSERT ON clients
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    SET next_id = COALESCE((SELECT MAX(CAST(SUBSTRING(client_id, 3) AS UNSIGNED)) FROM clients), 0) + 1;
    SET NEW.client_id = CONCAT('cl', LPAD(next_id, 6, '0'));
END;
//

DELIMITER ;


--- auto-generate manager ids
DELIMITER //

CREATE TRIGGER generate_manager_id
BEFORE INSERT ON managers
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    SET next_id = COALESCE((SELECT MAX(CAST(SUBSTRING(manager_id, 3) AS UNSIGNED)) FROM managers), 0) + 1;
    SET NEW.manager_id = CONCAT('mn', LPAD(next_id, 3, '0'));
END;
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_loan_insert
AFTER INSERT ON active_loans
FOR EACH ROW
BEGIN
    INSERT INTO loan_info (
        loan_id, client_id, national_id, phone_number, requested_amount, loan_purpose, duration, duration_period,
        date_applied, interest_rate, interest_rate_period, collateral_name, collateral_value, collateral_pic1,
        collateral_pic2, guarantor1_name, guarantor1_phone, guarantor2_name, guarantor2_phone, loan_status,
        onboarding_officer, created_at
    ) VALUES (
        NEW.loan_id, NEW.client_id, NEW.national_id, NEW.phone_number, NEW.requested_amount, NEW.loan_purpose,
        NEW.duration, NEW.duration_period, NEW.date_applied, NEW.interest_rate, NEW.interest_rate_period,
        NEW.collateral_name, NEW.collateral_value, NEW.collateral_pic1, NEW.collateral_pic2, NEW.guarantor1_name,
        NEW.guarantor1_phone, NEW.guarantor2_name, NEW.guarantor2_phone, NEW.loan_status, NEW.onboarding_officer,
        NEW.created_at
    );
END
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_loan_update
AFTER UPDATE ON active_loans
FOR EACH ROW
BEGIN
    INSERT INTO loan_info (
        loan_id, client_id, national_id, phone_number, requested_amount, loan_purpose, duration, duration_period,
        date_applied, interest_rate, interest_rate_period, collateral_name, collateral_value, collateral_pic1,
        collateral_pic2, guarantor1_name, guarantor1_phone, guarantor2_name, guarantor2_phone, loan_status,
        onboarding_officer, created_at, status_change
    ) VALUES (
        NEW.loan_id, NEW.client_id, NEW.national_id, NEW.phone_number, NEW.requested_amount, NEW.loan_purpose,
        NEW.duration, NEW.duration_period, NEW.date_applied, NEW.interest_rate, NEW.interest_rate_period,
        NEW.collateral_name, NEW.collateral_value, NEW.collateral_pic1, NEW.collateral_pic2, NEW.guarantor1_name,
        NEW.guarantor1_phone, NEW.guarantor2_name, NEW.guarantor2_phone, NEW.loan_status, NEW.onboarding_officer,
        NEW.created_at, CURRENT_TIMESTAMP
    );
END
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_loan_delete
AFTER DELETE ON active_loans
FOR EACH ROW
BEGIN
    INSERT INTO loan_info (
        loan_id, client_id, national_id, phone_number, requested_amount, loan_purpose, duration, duration_period,
        date_applied, interest_rate, interest_rate_period, collateral_name, collateral_value, collateral_pic1,
        collateral_pic2, guarantor1_name, guarantor1_phone, guarantor2_name, guarantor2_phone, loan_status,
        onboarding_officer, created_at, status_change
    ) VALUES (
        OLD.loan_id, OLD.client_id, OLD.national_id, OLD.phone_number, OLD.requested_amount, OLD.loan_purpose,
        OLD.duration, OLD.duration_period, OLD.date_applied, OLD.interest_rate, OLD.interest_rate_period,
        OLD.collateral_name, OLD.collateral_value, OLD.collateral_pic1, OLD.collateral_pic2, OLD.guarantor1_name,
        OLD.guarantor1_phone, OLD.guarantor2_name, OLD.guarantor2_phone, 'Deleted', OLD.onboarding_officer,
        OLD.created_at, CURRENT_TIMESTAMP
    );
END
//

DELIMITER ;