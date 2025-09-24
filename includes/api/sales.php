<?php
class SalesApi {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function handle($method, $segments) {
        switch ($method) {
            case 'GET':
                $this->getAllSales();
                break;
                
            case 'POST':
                if (!empty($segments) && $segments[0] === 'import-from-setoran') {
                    $this->importFromSetoran();
                } else {
                    $this->createSales();
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deleteSales($segments[0]);
                } else {
                    $this->sendError('Sales ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllSales() {
        $this->auth->requireAuth();
        
        $storeId = $_GET['storeId'] ?? null;
        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;
        
        try {
            $sql = "
                SELECT s.*, u.name as user_name, st.name as store_name
                FROM sales s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN stores st ON s.store_id = st.id
            ";
            $params = [];
            $conditions = [];
            
            if ($storeId) {
                $conditions[] = "s.store_id = ?";
                $params[] = $storeId;
            }
            
            if ($startDate) {
                $conditions[] = "DATE(s.date) >= ?";
                $params[] = $startDate;
            }
            
            if ($endDate) {
                $conditions[] = "DATE(s.date) <= ?";
                $params[] = $endDate;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            
            $sql .= " ORDER BY s.date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $sales = $stmt->fetchAll();
            
            $this->sendResponse($sales);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch sales: ' . $e->getMessage(), 500);
        }
    }
    
    private function createSales() {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data || !isset($data['store_id'], $data['total_sales'])) {
            $this->sendError('Missing required fields: store_id, total_sales', 400);
            return;
        }
        
        try {
            $salesId = $this->generateUuid();
            
            $stmt = $this->db->prepare("
                INSERT INTO sales (id, store_id, user_id, date, total_sales) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $salesId,
                $data['store_id'],
                $currentUser['id'],
                $data['date'] ?? date('Y-m-d H:i:s'),
                $data['total_sales']
            ]);
            
            $this->sendResponse(['message' => 'Sales record created successfully', 'sales_id' => $salesId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create sales record: ' . $e->getMessage(), 500);
        }
    }
    
    private function importFromSetoran() {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        $data = $this->getJsonInput();
        
        // This is a placeholder for setoran import functionality
        // In the full implementation, this would connect to the setoran API
        
        $this->sendResponse(['message' => 'Setoran import functionality placeholder', 'status' => 'not_implemented']);
    }
    
    private function deleteSales($salesId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        try {
            $stmt = $this->db->prepare("DELETE FROM sales WHERE id = ?");
            $stmt->execute([$salesId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Sales record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Sales record deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete sales record: ' . $e->getMessage(), 500);
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