<?php
class UserApi {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function handle($method, $segments) {
        switch ($method) {
            case 'GET':
                if (empty($segments)) {
                    $this->getAllUsers();
                } else {
                    $this->getUser($segments[0]);
                }
                break;
                
            case 'POST':
                $this->createUser();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updateUser($segments[0]);
                } else {
                    $this->sendError('User ID required', 400);
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deleteUser($segments[0]);
                } else {
                    $this->sendError('User ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllUsers() {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, GROUP_CONCAT(s.name) as store_names 
                FROM users u 
                LEFT JOIN user_stores us ON u.id = us.user_id 
                LEFT JOIN stores s ON us.store_id = s.id 
                GROUP BY u.id
            ");
            $stmt->execute();
            $users = $stmt->fetchAll();
            
            // Remove password from results
            foreach ($users as &$user) {
                unset($user['password']);
            }
            
            $this->sendResponse($users);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch users: ' . $e->getMessage(), 500);
        }
    }
    
    private function getUser($userId) {
        $this->auth->requireAuth();
        
        // Users can only view their own data unless they're manager/admin
        $currentUser = $this->auth->getCurrentUser();
        if ($userId !== $currentUser['id'] && !$this->auth->hasRole(['manager', 'administrasi'])) {
            $this->sendError('Access denied', 403);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, GROUP_CONCAT(s.name) as store_names 
                FROM users u 
                LEFT JOIN user_stores us ON u.id = us.user_id 
                LEFT JOIN stores s ON us.store_id = s.id 
                WHERE u.id = ?
                GROUP BY u.id
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->sendError('User not found', 404);
                return;
            }
            
            unset($user['password']);
            $this->sendResponse($user);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch user: ' . $e->getMessage(), 500);
        }
    }
    
    private function createUser() {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        $data = $this->getJsonInput();
        
        if (!$data || !isset($data['email'], $data['password'], $data['name'], $data['role'])) {
            $this->sendError('Missing required fields: email, password, name, role', 400);
            return;
        }
        
        try {
            // Check if user already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                $this->sendError('User with this email already exists', 400);
                return;
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $userId = $this->generateUuid();
            
            // Create user
            $stmt = $this->db->prepare("
                INSERT INTO users (id, email, password, name, role, phone, salary) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $data['email'],
                $hashedPassword,
                $data['name'],
                $data['role'],
                $data['phone'] ?? null,
                $data['salary'] ?? null
            ]);
            
            // Assign to stores if provided
            if (isset($data['store_ids']) && is_array($data['store_ids'])) {
                foreach ($data['store_ids'] as $storeId) {
                    $stmt = $this->db->prepare("
                        INSERT INTO user_stores (id, user_id, store_id) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$this->generateUuid(), $userId, $storeId]);
                }
            }
            
            $this->sendResponse(['message' => 'User created successfully', 'user_id' => $userId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create user: ' . $e->getMessage(), 500);
        }
    }
    
    private function updateUser($userId) {
        $this->auth->requireAuth();
        
        // Users can only update their own data unless they're manager/admin
        $currentUser = $this->auth->getCurrentUser();
        if ($userId !== $currentUser['id'] && !$this->auth->hasRole(['manager', 'administrasi'])) {
            $this->sendError('Access denied', 403);
            return;
        }
        
        $data = $this->getJsonInput();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            $setParts = [];
            $params = [];
            
            foreach (['name', 'phone', 'role', 'salary'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (isset($data['password'])) {
                $setParts[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $userId;
            
            $stmt = $this->db->prepare("
                UPDATE users SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('User not found or no changes made', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'User updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update user: ' . $e->getMessage(), 500);
        }
    }
    
    private function deleteUser($userId) {
        $this->auth->requireRole(['administrasi']);
        
        try {
            // Delete user store assignments first
            $stmt = $this->db->prepare("DELETE FROM user_stores WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('User not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'User deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete user: ' . $e->getMessage(), 500);
        }
    }
    
    private function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
    }
    
    private function sendError($message, $status = 400) {
        http_response_code($status);
        echo json_encode(['message' => $message]);
    }
    
    private function generateUuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
?>