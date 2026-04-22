CREATE DATABASE IF NOT EXISTS nette_zadani
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE nette_zadani;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` ENUM('user', 'admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username_attempt` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_success` TINYINT(1) NOT NULL,
  `ip_address` VARCHAR(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`username`, `first_name`, `last_name`, `email`, `phone`, `password`, `role`, `is_active`) VALUES
('admin', 'Hlavní', 'Administrátor', 'admin@example.com', '+420123456789', '$2y$10$TDs.8sre4gMLG/6rGBPDJus6Wy.huo0TWUUaEGxdDLktprZOeF9hu', 'admin', 1),
('user1', 'Pepa', 'Novák', 'pepa.novak@example.com', '+420987654321', '$2y$10$TDs.8sre4gMLG/6rGBPDJus6Wy.huo0TWUUaEGxdDLktprZOeF9hu', 'user', 1);
