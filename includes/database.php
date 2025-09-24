<?php
class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        // Try to connect, but handle all exceptions
        try {
            $dsn = "sqlite:" . __DIR__ . "/../" . DB_NAME;
            $this->connection = new PDO($dsn);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables on first connection
            $this->createTables();
            $this->initializeSampleData();
            
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
        // Create tables using SQLite syntax
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id TEXT PRIMARY KEY,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                name TEXT NOT NULL,
                role TEXT NOT NULL,
                phone TEXT,
                salary REAL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS stores (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                address TEXT,
                phone TEXT,
                manager TEXT,
                description TEXT,
                status TEXT DEFAULT 'active',
                entry_time_start TEXT DEFAULT '07:00',
                entry_time_end TEXT DEFAULT '09:00',
                exit_time_start TEXT DEFAULT '17:00',
                exit_time_end TEXT DEFAULT '19:00',
                timezone TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS user_stores (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                store_id INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )",

            "CREATE TABLE IF NOT EXISTS attendance (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                store_id INTEGER NOT NULL,
                date DATETIME NOT NULL,
                check_in TEXT,
                check_out TEXT,
                shift TEXT,
                lateness_minutes INTEGER,
                overtime_minutes INTEGER,
                break_duration INTEGER,
                overtime REAL
            )",

            "CREATE TABLE IF NOT EXISTS sales (
                id TEXT PRIMARY KEY,
                store_id INTEGER NOT NULL,
                user_id TEXT NOT NULL,
                date DATETIME NOT NULL,
                total_sales REAL
            )",

            "CREATE TABLE IF NOT EXISTS cashflow (
                id TEXT PRIMARY KEY,
                store_id INTEGER NOT NULL,
                category TEXT,
                type TEXT,
                amount REAL
            )",

            "CREATE TABLE IF NOT EXISTS payroll (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                store_id INTEGER NOT NULL,
                month TEXT,
                base_salary REAL
            )",

            "CREATE TABLE IF NOT EXISTS proposals (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                store_id INTEGER NOT NULL,
                title TEXT,
                category TEXT,
                estimated_cost REAL
            )",

            "CREATE TABLE IF NOT EXISTS overtime (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                store_id INTEGER NOT NULL,
                date DATETIME,
                hours REAL
            )"
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
        
        // Insert users one by one for SQLite
        $stmt = $this->connection->prepare("INSERT INTO users (id, email, password, name, role, salary) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$managerId, 'manager@spbu.com', $managerPassword, 'SPBU Manager', 'manager', 15000000]);
        $stmt->execute([$adminId, 'admin@spbu.com', $adminPassword, 'SPBU Administrator', 'administrasi', 12000000]);
        $stmt->execute([$putriId, 'putri@spbu.com', $putriPassword, 'Putri', 'staff', 8000000]);
        $stmt->execute([$hafizId, 'hafiz@spbu.com', $hafizPassword, 'Hafiz', 'staff', 8000000]);
        $stmt->execute([$endangId, 'endang@spbu.com', $endangPassword, 'Endang', 'staff', 8000000]);
        
        // Assign users to stores
        $stmt = $this->connection->prepare("INSERT INTO user_stores (id, user_id, store_id) VALUES (?, ?, ?)");
        $stmt->execute([$this->generateUuid(), $managerId, 1]);
        $stmt->execute([$this->generateUuid(), $managerId, 2]);
        $stmt->execute([$this->generateUuid(), $adminId, 1]);
        $stmt->execute([$this->generateUuid(), $adminId, 2]);
        $stmt->execute([$this->generateUuid(), $putriId, 1]);
        $stmt->execute([$this->generateUuid(), $hafizId, 1]);
        $stmt->execute([$this->generateUuid(), $endangId, 2]);
    }
    
    private function createSampleWallets() {
        // Wallets table is not defined in current schema - skip for now
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