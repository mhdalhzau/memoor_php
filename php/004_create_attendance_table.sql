-- Migration: Create attendance table
CREATE TABLE IF NOT EXISTS `attendance` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `store_id` INT NOT NULL,
    `date` TIMESTAMP NOT NULL,
    `check_in` VARCHAR(255),
    `check_out` VARCHAR(255),
    `shift` VARCHAR(255),
    `lateness_minutes` INT,
    `overtime_minutes` INT,
    `break_duration` INT,
    `overtime` DECIMAL(12,2),
    INDEX idx_attendance_user_id (`user_id`),
    INDEX idx_attendance_store_id (`store_id`),
    INDEX idx_attendance_date (`date`)
) ENGINE=MyISAM;