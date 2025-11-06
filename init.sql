-- SKidiyog MySQL Initialization Script
-- Run this to initialize the database schema

-- Create tables if they don't exist
CREATE TABLE IF NOT EXISTS `parks` (
    `idx` INT PRIMARY KEY,
    `name` VARCHAR(100),
    `cname` VARCHAR(100),
    `description` TEXT,
    `location` VARCHAR(255),
    `photo` VARCHAR(255),
    `about` LONGTEXT,
    `photo_section` LONGTEXT,
    `location_section` LONGTEXT,
    `slope_section` LONGTEXT,
    `ticket_section` LONGTEXT,
    `time_section` LONGTEXT,
    `access_section` LONGTEXT,
    `live_section` LONGTEXT,
    `rental_section` LONGTEXT,
    `delivery_section` LONGTEXT,
    `luggage_section` LONGTEXT,
    `workout_section` LONGTEXT,
    `remind_section` LONGTEXT,
    `join_section` LONGTEXT,
    `event_section` LONGTEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `instructors` (
    `idx` INT PRIMARY KEY,
    `name` VARCHAR(100),
    `cname` VARCHAR(100),
    `content` LONGTEXT,
    `photo` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `articles` (
    `idx` INT PRIMARY KEY,
    `title` VARCHAR(255),
    `tags` TEXT,
    `article` LONGTEXT,
    `keyword` TEXT,
    `timestamp` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create a simple test entry so we know tables exist
INSERT IGNORE INTO parks (idx, name, cname) VALUES (1, 'Test Park', '測試雪場');
