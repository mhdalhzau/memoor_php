<?php
class Auth {
    private $db;
    private $current_user;
    
    public function __construct($database) {
        $this->db = $database;
        $this->current_user = null;
        
        // Load current user from session
        if (isset($_SESSION['user_id'])) {
            $this->loadUserFromSession();
        }
    }
    
    public function register($email, $password, $name, $role, $phone = null, $salary = null) {
        try {
            // Check if user already exists
            if ($this->getUserByEmail($email)) {
                return ['success' => false, 'message' => 'User already exists'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
            
            // Generate UUID
            $userId = $this->generateUuid();
            
            // Create user
            $stmt = $this->db->prepare("
                INSERT INTO users (id, email, password, name, role, phone, salary) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$userId, $email, $hashedPassword, $name, $role, $phone, $salary]);
            
            return [
                'success' => true, 
                'message' => 'User created successfully',
                'user_id' => $userId
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $password) {
        // Demo login untuk testing PHP native
        $demoUsers = [
            'manager@spbu.com' => ['password' => 'manager123', 'name' => 'SPBU Manager', 'role' => 'manager', 'id' => '1'],
            'admin@spbu.com' => ['password' => 'admin123', 'name' => 'Admin SPBU', 'role' => 'administrasi', 'id' => '2'],
            'putri@spbu.com' => ['password' => 'putri123', 'name' => 'Putri', 'role' => 'staff', 'id' => '3']
        ];
        
        if (isset($demoUsers[$email]) && $demoUsers[$email]['password'] === $password) {
            $user = $demoUsers[$email];
            $user['email'] = $email;
            
            $_SESSION['user'] = $user;
            $_SESSION['authenticated'] = true;
            $this->current_user = $user;
            
            return ['success' => true, 'message' => 'Login berhasil', 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'Email atau password salah'];
        }
    }
    
    public function logout() {
        session_destroy();
        $this->current_user = null;
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    public function getCurrentUser() {
        return $this->current_user;
    }
    
    public function isAuthenticated() {
        return $this->current_user !== null;
    }
    
    public function hasRole($roles) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        return in_array($this->current_user['role'], $roles);
    }
    
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['message' => 'Authentication required']);
            exit();
        }
    }
    
    public function requireRole($roles) {
        $this->requireAuth();
        
        if (!$this->hasRole($roles)) {
            http_response_code(403);
            echo json_encode(['message' => 'Insufficient permissions']);
            exit();
        }
    }
    
    private function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    private function loadUserFromSession() {
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                $this->current_user = $user;
            } else {
                // Invalid session, clear it
                session_destroy();
            }
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