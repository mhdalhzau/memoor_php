-- Migration: Insert sample data
-- Insert sample stores
INSERT IGNORE INTO `stores` (`id`, `name`, `address`, `phone`, `manager`, `description`, `status`) VALUES
(1, 'Main Store', '123 Main Street', '021-1234567', 'SPBU Manager', 'Main store location with full services', 'active'),
(2, 'Branch Store', '456 Branch Avenue', '021-2345678', NULL, 'Branch store location', 'active');

-- Insert sample users with hashed passwords
-- Passwords: manager123, admin123, putri123, hafiz123, endang123
INSERT IGNORE INTO `users` (`id`, `email`, `password`, `name`, `role`, `salary`) VALUES
('manager-uuid-001', 'manager@spbu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqyT4VamVyPm8uO4gGy.Cq6', 'SPBU Manager', 'manager', 15000000.00),
('admin-uuid-002', 'admin@spbu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqyT4VamVyPm8uO4gGy.Cq6', 'SPBU Administrator', 'administrasi', 12000000.00),
('putri-uuid-003', 'putri@spbu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqyT4VamVyPm8uO4gGy.Cq6', 'Putri', 'staff', 8000000.00),
('hafiz-uuid-004', 'hafiz@spbu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqyT4VamVyPm8uO4gGy.Cq6', 'Hafiz', 'staff', 8000000.00),
('endang-uuid-005', 'endang@spbu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqyT4VamVyPm8uO4gGy.Cq6', 'Endang', 'staff', 8000000.00);

-- Assign users to stores
INSERT IGNORE INTO `user_stores` (`id`, `user_id`, `store_id`) VALUES
('us-001', 'manager-uuid-001', 1),
('us-002', 'manager-uuid-001', 2),
('us-003', 'admin-uuid-002', 1),
('us-004', 'admin-uuid-002', 2),
('us-005', 'putri-uuid-003', 1),
('us-006', 'hafiz-uuid-004', 1),
('us-007', 'endang-uuid-005', 2);