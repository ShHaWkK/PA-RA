CREATE DATABASE IF NOT EXISTS no_more_waste;
USE no_more_waste;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'volunteer', 'employee', 'manager', 'merchant') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des entreprises (companies)
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    siret VARCHAR(14) NOT NULL,
    renewal_date DATE NOT NULL,
    renewal_status ENUM('pending', 'notified', 'renewed') NOT NULL DEFAULT 'pending',
    last_notified TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Table de liaison entre utilisateurs et entreprises (user_companies)
CREATE TABLE IF NOT EXISTS user_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_id INT NOT NULL,
    role ENUM('employee', 'manager', 'merchant') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Table des disponibilités des bénévoles (availabilities)
CREATE TABLE IF NOT EXISTS availabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des compétences (skills)
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table de liaison entre bénévoles et compétences (user_skills)
CREATE TABLE IF NOT EXISTS user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

-- Table des produits (products)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    barcode VARCHAR(255) UNIQUE NOT NULL,
    qr_code_path VARCHAR(255),
    expiration_date DATE NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des collectes (collections)
CREATE TABLE IF NOT EXISTS collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    product_id INT NOT NULL,
    collection_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table des livraisons (deliveries)
CREATE TABLE IF NOT EXISTS deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_name VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    recipient_type ENUM('association', 'individual') NOT NULL,
    delivery_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(255) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des services (services)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    schedule TIMESTAMP NOT NULL,
    capacity INT NOT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    location VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des inscriptions aux services (service_registrations)
CREATE TABLE IF NOT EXISTS service_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des propositions de services (service_proposals)
CREATE TABLE IF NOT EXISTS service_proposals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('proposed', 'approved', 'rejected') NOT NULL DEFAULT 'proposed',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des stocks (stocks)
CREATE TABLE IF NOT EXISTS stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    entry_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    exit_date TIMESTAMP,
    availability ENUM('available', 'in_route', 'delivered') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion 

-- Produits
INSERT INTO products (name, barcode, expiration_date, quantity) VALUES 
('Product 1', '1234567890123', '2025-12-31', 100),
('Product 2', '1234567890124', '2025-12-31', 200),
('Product 3', '1234567890125', '2026-01-01', 300),
('Product 4', '1234567890126', '2026-06-01', 400),
('Product 5', '1234567890127', '2026-12-31', 500);

-- Utilisateurs
INSERT INTO users (first_name, last_name, email, phone_number, password, role, status) VALUES
('Admin', 'Admin', 'admin@admin.com', '1234567890', '$2y$10$KJ8zwrGJq9JfHywhUxxRheY.CgbYnBvGjUlhcXHup0DaF.IRtK/Sa', 'admin', 'approved'),
('John', 'Doe', 'john.doe@example.com', '0987654321', '$2y$10$KJ8zwrGJq9JfHywhUxxRheY.CgbYnBvGjUlhcXHup0DaF.IRtK/Sa', 'volunteer', 'approved'),
('Jane', 'Doe', 'jane.doe@example.com', '0987654322', '$2y$10$KJ8zwrGJq9JfHywhUxxRheY.CgbYnBvGjUlhcXHup0DaF.IRtK/Sa', 'employee', 'pending'),
('Alice', 'Smith', 'alice.smith@example.com', '0987654323', '$2y$10$KJ8zwrGJq9JfHywhUxxRheY.CgbYnBvGjUlhcXHup0DaF.IRtK/Sa', 'manager', 'approved'),
('Bob', 'Johnson', 'bob.johnson@example.com', '0987654324', '$2y$10$KJ8zwrGJq9JfHywhUxxRheY.CgbYnBvGjUlhcXHup0DaF.IRtK/Sa', 'merchant', 'pending');

-- Entreprises
INSERT INTO companies (name, address, contact_info, siret, renewal_date) VALUES 
('Company 1', 'Company Address 1', 'contact@company1.com', '12345678901234', '2024-07-01'),
('Company 2', 'Company Address 2', 'contact@company2.com', '12345678901235', '2024-08-01'),
('Company 3', 'Company Address 3', 'contact@company3.com', '12345678901236', '2024-09-01'),
('Company 4', 'Company Address 4', 'contact@company4.com', '12345678901237', '2024-10-01'),
('Company 5', 'Company Address 5', 'contact@company5.com', '12345678901238', '2024-11-01');

-- Liaison utilisateurs et entreprises
INSERT INTO user_companies (user_id, company_id, role) VALUES 
(2, 1, 'employee'),
(3, 2, 'manager'),
(4, 3, 'merchant'),
(5, 4, 'merchant'),
(2, 5, 'employee');

-- Compétences
INSERT INTO skills (name, description) VALUES 
('driver', 'Ability to drive various vehicles.'),
('cook', 'Ability to prepare meals and follow recipes.'),
('plumber', 'Ability to fix plumbing issues.'),
('electrician', 'Ability to fix electrical issues.'),
('teacher', 'Ability to teach various subjects.'),
('gardener', 'Ability to maintain gardens and landscapes.');

-- Liaison bénévoles et compétences
INSERT INTO user_skills (user_id, skill_id) VALUES 
(2, 1),
(2, 2),
(3, 3),
(3, 4),
(4, 5),
(5, 6);

-- Disponibilités des bénévoles
INSERT INTO availabilities (user_id, day_of_week, start_time, end_time) VALUES 
(2, 'Monday', '09:00:00', '12:00:00'),
(2, 'Wednesday', '14:00:00', '18:00:00'),
(3, 'Tuesday', '10:00:00', '13:00:00'),
(3, 'Thursday', '15:00:00', '19:00:00'),
(4, 'Friday', '08:00:00', '11:00:00'),
(5, 'Saturday', '12:00:00', '16:00:00');

-- Insertion des entreprises avec une date de renouvellement valide
INSERT INTO companies (name, address, contact_info, siret, renewal_date) VALUES 
('Company 6', 'Company Address 6', 'contact@company6.com', '12345678901239', '2024-12-01'),
('Company 7', 'Company Address 7', 'contact@company7.com', '12345678901240', '2025-01-01'),
('Company 8', 'Company Address 8', 'contact@company8.com', '12345678901241', '2025-02-01'),
('Company 9', 'Company Address 9', 'contact@company9.com', '12345678901242', '2025-03-01'),
('Company 10', 'Company Address 10', 'contact@company10.com', '12345678901243', '2025-04-01');

-- Insertion des compétences supplémentaires
INSERT INTO skills (name, description) VALUES 
('programmer', 'Ability to write and maintain computer programs.'),
('designer', 'Ability to create visual designs and graphics.'),
('mechanic', 'Ability to repair and maintain vehicles and machinery.'),
('nurse', 'Ability to provide medical care and assistance.');