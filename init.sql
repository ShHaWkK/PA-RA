CREATE DATABASE IF NOT EXISTS no_more_waste;
USE no_more_waste;

CREATE TABLE IF NOT EXISTS commercants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    infos_contact VARCHAR(255) NOT NULL,
    date_expiration_adhesion DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    code_barres VARCHAR(255) UNIQUE NOT NULL,
    date_expiration DATE NOT NULL,
    quantite INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS collectes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commercant_id INT NOT NULL,
    produit_id INT NOT NULL,
    date_collecte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (commercant_id) REFERENCES commercants(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS livraisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_route VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    type_destinataire ENUM('association', 'particulier') NOT NULL,
    date_livraison TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(255) NOT NULL,
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS benevoles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    competences JSON NOT NULL,
    disponibilites JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    horaire TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inscriptions_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    benevole_id INT NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (benevole_id) REFERENCES benevoles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'commercant', 'benevole', 'client') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    date_entree TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_sortie TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- Ajout des commerçants
INSERT INTO commercants (nom, adresse, infos_contact, date_expiration_adhesion) VALUES 
('Commerçant 1', 'Adresse 1', 'Contact 1', '2025-06-30'),
('Commerçant 2', 'Adresse 2', 'Contact 2', '2025-06-30');

-- Ajout des produits
INSERT INTO produits (nom, code_barres, date_expiration, quantite) VALUES 
('Produit 1', '1234567890123', '2025-12-31', 100),
('Produit 2', '1234567890124', '2025-12-31', 200);

-- Ajout des bénévoles
INSERT INTO benevoles (nom, competences, disponibilites) VALUES 
('Bénévole 1', '["chauffeur", "cuisinier"]', '["lundi", "mercredi"]'),
('Bénévole 2', '["plombier"]', '["mardi", "jeudi"]');

-- Ajout des services
INSERT INTO services (nom, description, horaire) VALUES 
('Cours de cuisine', 'Apprenez à cuisiner avec des produits anti-gaspi', '2024-07-01 10:00:00'),
('Conseils anti-gaspi', 'Des conseils pour éviter le gaspillage au quotidien', '2024-07-02 14:00:00');

-- Ajout des utilisateurs
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES 
('Admin', 'admin@admin.com', 'password', 'admin'),
('Commerçant 1', 'commercant1@commercant.com', 'password', 'commercant'),
('Bénévole 1', 'benevole1@benevole.com', 'password', 'benevole'),
('Client 1', 'client@example.com', 'password', 'client');
