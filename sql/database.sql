-- DietProCenter database schema
-- Import with: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS dietprocenter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dietprocenter;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    consultation_type VARCHAR(50) NOT NULL,
    preferred_date DATE NOT NULL,
    message TEXT NULL,
    status ENUM('nouveau', 'confirme', 'annule') NOT NULL DEFAULT 'nouveau',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin login: username "admin" / password "admin123"
-- Change this password after first login (the hash below is verified to match "admin123").
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2b$12$eAvom.LjUcPewRTmOZ0aQePMe/daVCTM3DvZ5PSNbB0ZUN1jwv2DC')
ON DUPLICATE KEY UPDATE username = username;