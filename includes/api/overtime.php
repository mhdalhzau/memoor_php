<?php
class OvertimeApi {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function handle($method, $segments) {
        switch ($method) {
            case 'GET':
                $this->getAllOvertime();
                break;
                
            case 'POST':
                $this->createOvertime();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updateOvertime($segments[0]);
                } else {
                    $this->sendError('Overtime ID required', 400);
                }
                break;
                
            case 'PATCH':
                if (!empty($segments) && isset($segments[1]) && $segments[1] === 'approve') {
                    $this->approveOvertime($segments[0]);
                } else {
                    $this->sendError('Invalid patch operation', 400);
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deleteOvertime($segments[0]);
                } else {
                    $this->sendError('Overtime ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllOvertime() {
        $this->auth->requireAuth();
        
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, u.name as employee_name, s.name as store_name
                FROM overtime o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN stores s ON o.store_id = s.id
                ORDER BY o.date DESC
            ");
            $stmt->execute();
            $overtime = $stmt->fetchAll();
            
            $this->sendResponse($overtime);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch overtime: ' . $e->getMessage(), 500);
        }
    }
    
    private function createOvertime() {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data || !isset($data['store_id'], $data['date'], $data['hours'])) {
            $this->sendError('Missing required fields: store_id, date, hours', 400);
            return;
        }
        
        try {
            $overtimeId = $this->generateUuid();
            
            $stmt = $this->db->prepare("
                INSERT INTO overtime (id, user_id, store_id, date, hours) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $overtimeId,
                $currentUser['id'],
                $data['store_id'],
                $data['date'],
                $data['hours']
            ]);
            
            $this->sendResponse(['message' => 'Overtime record created successfully', 'overtime_id' => $overtimeId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create overtime record: ' . $e->getMessage(), 500);
        }
    }
    
    private function updateOvertime($overtimeId) {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            // Check if user owns the overtime record or is manager/admin
            $stmt = $this->db->prepare("SELECT user_id FROM overtime WHERE id = ?");
            $stmt->execute([$overtimeId]);
            $overtime = $stmt->fetch();
            
            if (!$overtime) {
                $this->sendError('Overtime record not found', 404);
                return;
            }
            
            if ($overtime['user_id'] !== $currentUser['id'] && !$this->auth->hasRole(['manager', 'administrasi'])) {
                $this->sendError('Access denied', 403);
                return;
            }
            
            $setParts = [];
            $params = [];
            
            foreach (['date', 'hours'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $overtimeId;
            
            $stmt = $this->db->prepare("
                UPDATE overtime SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            $this->sendResponse(['message' => 'Overtime record updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update overtime record: ' . $e->getMessage(), 500);
        }
    }
    
    private function approveOvertime($overtimeId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        try {
            $stmt = $this->db->prepare("
                UPDATE overtime SET status = 'approved' WHERE id = ?
            ");
            $stmt->execute([$overtimeId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Overtime record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Overtime approved successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to approve overtime: ' . $e->getMessage(), 500);
        }
    }
    
    private function deleteOvertime($overtimeId) {
        $this->auth->requireAuth();
        
        $currentUser = $this->auth->getCurrentUser();
        
        try {
            // Check if user owns the overtime record or is admin
            $stmt = $this->db->prepare("SELECT user_id FROM overtime WHERE id = ?");
            $stmt->execute([$overtimeId]);
            $overtime = $stmt->fetch();
            
            if (!$overtime) {
                $this->sendError('Overtime record not found', 404);
                return;
            }
            
            if ($overtime['user_id'] !== $currentUser['id'] && !$this->auth->hasRole(['administrasi'])) {
                $this->sendError('Access denied', 403);
                return;
            }
            
            $stmt = $this->db->prepare("DELETE FROM overtime WHERE id = ?");
            $stmt->execute([$overtimeId]);
            
            $this->sendResponse(['message' => 'Overtime record deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete overtime record: ' . $e->getMessage(), 500);
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