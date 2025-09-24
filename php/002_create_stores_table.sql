-- Migration: Create stores table
CREATE TABLE IF NOT EXISTS `stores` (
    `id` INT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255),
    `phone` VARCHAR(50),
    `manager` VARCHAR(255),
    `description` VARCHAR(255),
    `status` VARCHAR(50) DEFAULT 'active',
    `entry_time_start` VARCHAR(255) DEFAULT '07:00',
    `entry_time_end` VARCHAR(255) DEFAULT '09:00',
    `exit_time_start` VARCHAR(255) DEFAULT '17:00',
    `exit_time_end` VARCHAR(255) DEFAULT '19:00',
    `timezone` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM;