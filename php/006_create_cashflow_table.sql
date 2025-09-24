-- Migration: Create cashflow table
CREATE TABLE IF NOT EXISTS `cashflow` (
    `id` CHAR(36) PRIMARY KEY,
    `store_id` INT NOT NULL,
    `category` VARCHAR(255),
    `type` VARCHAR(255),
    `amount` DECIMAL(12,2),
    INDEX idx_cashflow_store_id (`store_id`)
) ENGINE=MyISAM;