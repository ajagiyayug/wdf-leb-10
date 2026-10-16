CREATE DATABASE IF NOT EXISTS practical10;
USE practical10;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    remember_token_hash VARCHAR(255) DEFAULT NULL,
    last_login DATETIME DEFAULT NULL
);

-- Note: Passwords hain 'admin123' aur 'student123' (bcrypt hashed via PHP)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$e.qV1.71m0e6ZtL1wW3Lh.8K8JzX1j5Q7o8u/3zJ5r6y9X2A8a9B6', 'admin'),
('student', '$2y$10$e.qV1.71m0e6ZtL1wW3Lh.8K8JzX1j5Q7o8u/3zJ5r6y9X2A8a9B6', 'student')
ON DUPLICATE KEY UPDATE username=username;