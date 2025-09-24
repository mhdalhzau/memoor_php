<?php
class StoreApi {
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
                    $this->getAllStores();
                } else {
                    $this->getStore($segments[0]);
                }
                break;
                
            case 'POST':
                $this->createStore();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updateStore($segments[0]);
                } else {
                    $this->sendError('Store ID required', 400);
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deleteStore($segments[0]);
                } else {
                    $this->sendError('Store ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllStores() {
        $this->auth->requireAuth();
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM stores ORDER BY name");
            $stmt->execute();
            $stores = $stmt->fetchAll();
            
            $this->sendResponse($stores);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch stores: ' . $e->getMessage(), 500);
        }
    }
    
    private function getStore($storeId) {
        $this->auth->requireAuth();
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM stores WHERE id = ?");
            $stmt->execute([$storeId]);
            $store = $stmt->fetch();
            
            if (!$store) {
                $this->sendError('Store not found', 404);
                return;
            }
            
            $this->sendResponse($store);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch store: ' . $e->getMessage(), 500);
        }
    }
    
    private function createStore() {
        $this->auth->requireRole(['administrasi']);
        
        $data = $this->getJsonInput();
        
        if (!$data || !isset($data['id'], $data['name'])) {
            $this->sendError('Missing required fields: id, name', 400);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO stores (id, name, address, phone, manager, description, status, entry_time_start, entry_time_end, exit_time_start, exit_time_end, timezone) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['id'],
                $data['name'],
                $data['address'] ?? null,
                $data['phone'] ?? null,
                $data['manager'] ?? null,
                $data['description'] ?? null,
                $data['status'] ?? 'active',
                $data['entry_time_start'] ?? '07:00',
                $data['entry_time_end'] ?? '09:00',
                $data['exit_time_start'] ?? '17:00',
                $data['exit_time_end'] ?? '19:00',
                $data['timezone'] ?? 'Asia/Jakarta'
            ]);
            
            $this->sendResponse(['message' => 'Store created successfully', 'store_id' => $data['id']], 201);
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $this->sendError('Store with this ID already exists', 400);
            } else {
                $this->sendError('Failed to create store: ' . $e->getMessage(), 500);
            }
        }
    }
    
    private function updateStore($storeId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        $data = $this->getJsonInput();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            $setParts = [];
            $params = [];
            
            foreach (['name', 'address', 'phone', 'manager', 'description', 'status', 'entry_time_start', 'entry_time_end', 'exit_time_start', 'exit_time_end', 'timezone'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $storeId;
            
            $stmt = $this->db->prepare("
                UPDATE stores SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Store not found or no changes made', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Store updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update store: ' . $e->getMessage(), 500);
        }
    }
    
    private function deleteStore($storeId) {
        $this->auth->requireRole(['administrasi']);
        
        try {
            $stmt = $this->db->prepare("DELETE FROM stores WHERE id = ?");
            $stmt->execute([$storeId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Store not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Store deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete store: ' . $e->getMessage(), 500);
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
}
?>