-- Migration: Create proposals table
CREATE TABLE IF NOT EXISTS `proposals` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `store_id` INT NOT NULL,
    `title` VARCHAR(255),
    `category` VARCHAR(255),
    `estimated_cost` DECIMAL(12,2),
    INDEX idx_proposals_user_id (`user_id`),
    INDEX idx_proposals_store_id (`store_id`)
) ENGINE=MyISAM;