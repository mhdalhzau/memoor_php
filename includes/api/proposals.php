<?php
class ProposalApi {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function handle($method, $segments) {
        switch ($method) {
            case 'GET':
                $this->getAllProposals();
                break;
                
            case 'POST':
                $this->createProposal();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updateProposal($segments[0]);
                } else {
                    $this->sendError('Proposal ID required', 400);
                }
                break;
                
            case 'DELETE':
                if (!empty($segments)) {
                    $this->deleteProposal($segments[0]);
                } else {
                    $this->sendError('Proposal ID required', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllProposals() {
        $this->auth->requireAuth();
        
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.name as employee_name, s.name as store_name
                FROM proposals p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN stores s ON p.store_id = s.id
                ORDER BY p.id DESC
            ");
            $stmt->execute();
            $proposals = $stmt->fetchAll();
            
            $this->sendResponse($proposals);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch proposals: ' . $e->getMessage(), 500);
        }
    }
    
    private function createProposal() {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data || !isset($data['store_id'], $data['title'], $data['category'])) {
            $this->sendError('Missing required fields: store_id, title, category', 400);
            return;
        }
        
        try {
            $proposalId = $this->generateUuid();
            
            $stmt = $this->db->prepare("
                INSERT INTO proposals (id, user_id, store_id, title, category, estimated_cost) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $proposalId,
                $currentUser['id'],
                $data['store_id'],
                $data['title'],
                $data['category'],
                $data['estimated_cost'] ?? null
            ]);
            
            $this->sendResponse(['message' => 'Proposal created successfully', 'proposal_id' => $proposalId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create proposal: ' . $e->getMessage(), 500);
        }
    }
    
    private function updateProposal($proposalId) {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            // Check if user owns the proposal or is manager/admin
            $stmt = $this->db->prepare("SELECT user_id FROM proposals WHERE id = ?");
            $stmt->execute([$proposalId]);
            $proposal = $stmt->fetch();
            
            if (!$proposal) {
                $this->sendError('Proposal not found', 404);
                return;
            }
            
            if ($proposal['user_id'] !== $currentUser['id'] && !$this->auth->hasRole(['manager', 'administrasi'])) {
                $this->sendError('Access denied', 403);
                return;
            }
            
            $setParts = [];
            $params = [];
            
            foreach (['title', 'category', 'estimated_cost'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $proposalId;
            
            $stmt = $this->db->prepare("
                UPDATE proposals SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            $this->sendResponse(['message' => 'Proposal updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update proposal: ' . $e->getMessage(), 500);
        }
    }
    
    private function deleteProposal($proposalId) {
        $this->auth->requireAuth();
        
        $currentUser = $this->auth->getCurrentUser();
        
        try {
            // Check if user owns the proposal or is admin
            $stmt = $this->db->prepare("SELECT user_id FROM proposals WHERE id = ?");
            $stmt->execute([$proposalId]);
            $proposal = $stmt->fetch();
            
            if (!$proposal) {
                $this->sendError('Proposal not found', 404);
                return;
            }
            
            if ($proposal['user_id'] !== $currentUser['id'] && !$this->auth->hasRole(['administrasi'])) {
                $this->sendError('Access denied', 403);
                return;
            }
            
            $stmt = $this->db->prepare("DELETE FROM proposals WHERE id = ?");
            $stmt->execute([$proposalId]);
            
            $this->sendResponse(['message' => 'Proposal deleted successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to delete proposal: ' . $e->getMessage(), 500);
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