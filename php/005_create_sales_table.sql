-- Migration: Create sales table
CREATE TABLE IF NOT EXISTS `sales` (
    `id` CHAR(36) PRIMARY KEY,
    `store_id` INT NOT NULL,
    `user_id` CHAR(36) NOT NULL,
    `date` TIMESTAMP NOT NULL,
    `total_sales` DECIMAL(12,2),
    INDEX idx_sales_user_id (`user_id`),
    INDEX idx_sales_store_id (`store_id`),
    INDEX idx_sales_date (`date`)
) ENGINE=MyISAM;