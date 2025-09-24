-- Migration: Create payroll table
CREATE TABLE IF NOT EXISTS `payroll` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `store_id` INT NOT NULL,
    `month` VARCHAR(50),
    `base_salary` DECIMAL(12,2),
    INDEX idx_payroll_user_id (`user_id`),
    INDEX idx_payroll_store_id (`store_id`)
) ENGINE=MyISAM;