<?php
class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        // Try to connect, but handle all exceptions
        try {
            mysqli_report(MYSQLI_REPORT_OFF); // Disable mysqli error reporting
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            
            // Check connection
            if ($this->connection->connect_error) {
                throw new Exception($this->connection->connect_error);
            }
            
            // Set charset if connected
            $this->connection->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            // Connection failed, switch to demo mode
            $this->connection = null;
            error_log("Database connection failed, running in demo mode: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function isConnected() {
        return $this->connection !== null;
    }
    
    public function query($sql) {
        if ($this->connection) {
            return $this->connection->query($sql);
        }
        return false;
    }
    
    public function prepare($sql) {
        if ($this->connection) {
            return $this->connection->prepare($sql);
        }
        return false;
    }
    
    private function createTables() {
        // Create tables using MySQL syntax - exactly matching user's schema (no foreign keys for MyISAM)
        $tables = [
            "CREATE TABLE IF NOT EXISTS `users` (
                `id` CHAR(36) PRIMARY KEY,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `role` VARCHAR(100) NOT NULL,
                `phone` VARCHAR(50),
                `salary` DECIMAL(12,2),
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE INDEX idx_users_email (`email`)
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `stores` (
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
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `user_stores` (
                `id` CHAR(36) PRIMARY KEY,
                `user_id` CHAR(36) NOT NULL,
                `store_id` INT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_stores_user_id (`user_id`),
                INDEX idx_user_stores_store_id (`store_id`)
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `attendance` (
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
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `sales` (
                `id` CHAR(36) PRIMARY KEY,
                `store_id` INT NOT NULL,
                `user_id` CHAR(36) NOT NULL,
                `date` TIMESTAMP NOT NULL,
                `total_sales` DECIMAL(12,2),
                INDEX idx_sales_user_id (`user_id`),
                INDEX idx_sales_store_id (`store_id`),
                INDEX idx_sales_date (`date`)
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `cashflow` (
                `id` CHAR(36) PRIMARY KEY,
                `store_id` INT NOT NULL,
                `category` VARCHAR(255),
                `type` VARCHAR(255),
                `amount` DECIMAL(12,2),
                INDEX idx_cashflow_store_id (`store_id`)
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `payroll` (
                `id` CHAR(36) PRIMARY KEY,
                `user_id` CHAR(36) NOT NULL,
                `store_id` INT NOT NULL,
                `month` VARCHAR(50),
                `base_salary` DECIMAL(12,2),
                INDEX idx_payroll_user_id (`user_id`),
                INDEX idx_payroll_store_id (`store_id`)
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `proposals` (
                `id` CHAR(36) PRIMARY KEY,
                `user_id` CHAR(36) NOT NULL,
                `store_id` INT NOT NULL,
                `title` VARCHAR(255),
                `category` VARCHAR(255),
                `estimated_cost` DECIMAL(12,2),
                INDEX idx_proposals_user_id (`user_id`),
                INDEX idx_proposals_store_id (`store_id`)
            ) ENGINE=MyISAM",

            "CREATE TABLE IF NOT EXISTS `overtime` (
                `id` CHAR(36) PRIMARY KEY,
                `user_id` CHAR(36) NOT NULL,
                `store_id` INT NOT NULL,
                `date` TIMESTAMP,
                `hours` DECIMAL(12,2),
                INDEX idx_overtime_user_id (`user_id`),
                INDEX idx_overtime_store_id (`store_id`),
                INDEX idx_overtime_date (`date`)
            ) ENGINE=MyISAM"
        ];
        
        foreach ($tables as $sql) {
            $this->connection->exec($sql);
        }
    }
    
    private function initializeSampleData() {
        // Check if data already exists
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM stores");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            return; // Data already exists
        }
        
        // Create sample stores
        $this->connection->exec("
            INSERT INTO stores (id, name, address, phone, manager, description, status) VALUES
            (1, 'Main Store', '123 Main Street', '021-1234567', 'SPBU Manager', 'Main store location with full services', 'active'),
            (2, 'Branch Store', '456 Branch Avenue', '021-2345678', NULL, 'Branch store location', 'active')
        ");
        
        // Create sample users with hashed passwords
        $managerPassword = password_hash('manager123', PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        $putriPassword = password_hash('putri123', PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        $hafizPassword = password_hash('hafiz123', PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        $endangPassword = password_hash('endang123', PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        
        $managerId = $this->generateUuid();
        $adminId = $this->generateUuid();
        $putriId = $this->generateUuid();
        $hafizId = $this->generateUuid();
        $endangId = $this->generateUuid();
        
        $stmt = $this->connection->prepare("
            INSERT INTO users (id, email, password, name, role, salary) VALUES
            (?, 'manager@spbu.com', ?, 'SPBU Manager', 'manager', 15000000),
            (?, 'admin@spbu.com', ?, 'SPBU Administrator', 'administrasi', 12000000),
            (?, 'putri@spbu.com', ?, 'Putri', 'staff', 8000000),
            (?, 'hafiz@spbu.com', ?, 'Hafiz', 'staff', 8000000),
            (?, 'endang@spbu.com', ?, 'Endang', 'staff', 8000000)
        ");
        $stmt->execute([$managerId, $managerPassword, $adminId, $adminPassword, $putriId, $putriPassword, $hafizId, $hafizPassword, $endangId, $endangPassword]);
        
        // Assign users to stores
        $this->connection->exec("
            INSERT INTO user_stores (user_id, store_id) VALUES
            ('$managerId', 1), ('$managerId', 2),
            ('$adminId', 1), ('$adminId', 2),
            ('$putriId', 1),
            ('$hafizId', 1),
            ('$endangId', 2)
        ");
        
        // Create sample wallets
        $this->createSampleWallets();
    }
    
    private function createSampleWallets() {
        $walletData = [
            [1, 'Bank BCA', 'bank', 5000000, '1234567890', 'Rekening utama Bank BCA'],
            [1, 'Kas Tunai', 'cash', 500000, null, 'Kas tunai toko'],
            [1, 'OVO', 'ewallet', 250000, '08123456789', 'E-Wallet OVO toko'],
            [2, 'Bank BCA', 'bank', 3000000, '0987654321', 'Rekening utama Bank BCA'],
            [2, 'Kas Tunai', 'cash', 300000, null, 'Kas tunai toko'],
            [2, 'DANA', 'ewallet', 150000, '08987654321', 'E-Wallet DANA toko']
        ];
        
        $stmt = $this->connection->prepare("
            INSERT INTO wallets (store_id, name, type, balance, account_number, description) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($walletData as $wallet) {
            $stmt->execute($wallet);
        }
    }
    
    private function generateUuid() {
        // Generate UUID v4 compatible with MySQL CHAR(36)
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
?>