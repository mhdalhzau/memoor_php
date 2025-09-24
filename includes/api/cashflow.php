<?php
class CashflowApi {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function handle($method, $segments) {
        switch ($method) {
            case 'GET':
                $this->getAllCashflow();
                break;
                
            case 'POST':
                $this->createCashflow();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updateCashflow($segments[0]);
                } else {
                    $this->sendError('Cashflow ID required', 400);
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deleteCashflow($segments[0]);
                } else {
                    $this->sendError('Cashflow ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllCashflow() {
        $this->auth->requireAuth();
        
        $storeId = $_GET['storeId'] ?? null;
        
        try {
            $sql = "
                SELECT c.*, s.name as store_name
                FROM cashflow c
                LEFT JOIN stores s ON c.store_id = s.id
            ";
            $params = [];
            
            if ($storeId) {
                $sql .= " WHERE c.store_id = ?";
                $params[] = $storeId;
            }
            
            $sql .= " ORDER BY c.id DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $cashflow = $stmt->fetchAll();
            
            $this->sendResponse($cashflow);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch cashflow: ' . $e->getMessage(), 500);
        }
    }
    
    private function createCashflow() {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        
        if (!$data || !isset($data['store_id'], $data['category'], $data['type'], $data['amount'])) {
            $this->sendError('Missing required fields: store_id, category, type, amount', 400);
            return;
        }
        
        try {
            $cashflowId = $this->generateUuid();
            
            $stmt = $this->db->prepare("
                INSERT INTO cashflow (id, store_id, category, type, amount) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $cashflowId,
                $data['store_id'],
                $data['category'],
                $data['type'],
                $data['amount']
            ]);
            
            $this->sendResponse(['message' => 'Cashflow record created successfully', 'cashflow_id' => $cashflowId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create cashflow record: ' . $e->getMessage(), 500);
        }
    }
    
    private function updateCashflow($cashflowId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        $data = $this->getJsonInput();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            $setParts = [];
            $params = [];
            
            foreach (['category', 'type', 'amount'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $cashflowId;
            
            $stmt = $this->db->prepare("
                UPDATE cashflow SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Cashflow record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Cashflow record updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update cashflow record: ' . $e->getMessage(), 500);
        }
    }
    
    private function deleteCashflow($cashflowId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        try {
            $stmt = $this->db->prepare("DELETE FROM cashflow WHERE id = ?");
            $stmt->execute([$cashflowId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Cashflow record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Cashflow record deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete cashflow record: ' . $e->getMessage(), 500);
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