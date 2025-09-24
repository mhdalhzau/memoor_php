<?php
class PayrollApi {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function handle($method, $segments) {
        switch ($method) {
            case 'GET':
                $this->getAllPayroll();
                break;
                
            case 'POST':
                $this->createPayroll();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updatePayroll($segments[0]);
                } else {
                    $this->sendError('Payroll ID required', 400);
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deletePayroll($segments[0]);
                } else {
                    $this->sendError('Payroll ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllPayroll() {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.name as employee_name, s.name as store_name
                FROM payroll p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN stores s ON p.store_id = s.id
                ORDER BY p.month DESC
            ");
            $stmt->execute();
            $payroll = $stmt->fetchAll();
            
            $this->sendResponse($payroll);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch payroll: ' . $e->getMessage(), 500);
        }
    }
    
    private function createPayroll() {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        $data = $this->getJsonInput();
        
        if (!$data || !isset($data['user_id'], $data['store_id'], $data['month'], $data['base_salary'])) {
            $this->sendError('Missing required fields: user_id, store_id, month, base_salary', 400);
            return;
        }
        
        try {
            $payrollId = $this->generateUuid();
            
            $stmt = $this->db->prepare("
                INSERT INTO payroll (id, user_id, store_id, month, base_salary) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $payrollId,
                $data['user_id'],
                $data['store_id'],
                $data['month'],
                $data['base_salary']
            ]);
            
            $this->sendResponse(['message' => 'Payroll record created successfully', 'payroll_id' => $payrollId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create payroll record: ' . $e->getMessage(), 500);
        }
    }
    
    private function updatePayroll($payrollId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        $data = $this->getJsonInput();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            $setParts = [];
            $params = [];
            
            foreach (['month', 'base_salary'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $payrollId;
            
            $stmt = $this->db->prepare("
                UPDATE payroll SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Payroll record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Payroll record updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update payroll record: ' . $e->getMessage(), 500);
        }
    }
    
    private function deletePayroll($payrollId) {
        $this->auth->requireRole(['administrasi']);
        
        try {
            $stmt = $this->db->prepare("DELETE FROM payroll WHERE id = ?");
            $stmt->execute([$payrollId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Payroll record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Payroll record deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete payroll record: ' . $e->getMessage(), 500);
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