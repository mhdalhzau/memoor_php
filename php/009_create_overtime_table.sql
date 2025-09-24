-- Migration: Create overtime table
CREATE TABLE IF NOT EXISTS `overtime` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `store_id` INT NOT NULL,
    `date` TIMESTAMP,
    `hours` DECIMAL(12,2),
    INDEX idx_overtime_user_id (`user_id`),
    INDEX idx_overtime_store_id (`store_id`),
    INDEX idx_overtime_date (`date`)
) ENGINE=MyISAM;