-- Migration: Create user_stores table
CREATE TABLE IF NOT EXISTS `user_stores` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `store_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_stores_user_id (`user_id`),
    INDEX idx_user_stores_store_id (`store_id`)
) ENGINE=MyISAM;