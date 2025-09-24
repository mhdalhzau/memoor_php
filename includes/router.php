<?php
require_once 'includes/api/users.php';
require_once 'includes/api/stores.php';
require_once 'includes/api/attendance.php';
require_once 'includes/api/sales.php';
require_once 'includes/api/cashflow.php';
require_once 'includes/api/payroll.php';
require_once 'includes/api/proposals.php';
require_once 'includes/api/overtime.php';

class Router {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function route($method, $uri) {
        // Remove leading slash and split path
        $path = trim($uri, '/');
        $segments = explode('/', $path);
        
        // Set content type for API responses
        header('Content-Type: application/json');
        
        try {
            // Handle authentication routes
            if ($segments[0] === 'api' && $segments[1] === 'auth') {
                $this->handleAuth($method, array_slice($segments, 2));
                return;
            }
            
            // Handle API routes
            if ($segments[0] === 'api') {
                $this->handleApi($method, array_slice($segments, 1));
                return;
            }
            
            // Serve static files or frontend
            $this->handleStatic($uri);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Internal server error: ' . $e->getMessage()]);
        }
    }
    
    private function handleAuth($method, $segments) {
        switch ($method) {
            case 'POST':
                if ($segments[0] === 'register') {
                    $this->register();
                } elseif ($segments[0] === 'login') {
                    $this->login();
                } elseif ($segments[0] === 'logout') {
                    $this->logout();
                } else {
                    $this->notFound();
                }
                break;
                
            case 'GET':
                if ($segments[0] === 'me') {
                    $this->getCurrentUser();
                } else {
                    $this->notFound();
                }
                break;
                
            default:
                $this->methodNotAllowed();
        }
    }
    
    private function handleApi($method, $segments) {
        $resource = $segments[0] ?? '';
        
        switch ($resource) {
            case 'users':
                $userApi = new UserApi($this->db, $this->auth);
                $userApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'stores':
                $storeApi = new StoreApi($this->db, $this->auth);
                $storeApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'attendance':
                $attendanceApi = new AttendanceApi($this->db, $this->auth);
                $attendanceApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'sales':
                $salesApi = new SalesApi($this->db, $this->auth);
                $salesApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'cashflow':
                $cashflowApi = new CashflowApi($this->db, $this->auth);
                $cashflowApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'payroll':
                $payrollApi = new PayrollApi($this->db, $this->auth);
                $payrollApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'proposals':
                $proposalApi = new ProposalApi($this->db, $this->auth);
                $proposalApi->handle($method, array_slice($segments, 1));
                break;
                
            case 'overtime':
                $overtimeApi = new OvertimeApi($this->db, $this->auth);
                $overtimeApi->handle($method, array_slice($segments, 1));
                break;
                
            default:
                $this->notFound();
        }
    }
    
    private function handleStatic($uri) {
        // For now, return a simple message
        // In production, you'd serve your React build files here
        if ($uri === '/' || $uri === '') {
            echo json_encode(['message' => 'SPBU Management API', 'version' => APP_VERSION]);
        } else {
            $this->notFound();
        }
    }
    
    // Authentication methods
    private function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['email'], $data['password'], $data['name'], $data['role'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields']);
            return;
        }
        
        $result = $this->auth->register(
            $data['email'],
            $data['password'],
            $data['name'],
            $data['role'],
            $data['phone'] ?? null,
            $data['salary'] ?? null
        );
        
        http_response_code($result['success'] ? 201 : 400);
        echo json_encode($result);
    }
    
    private function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Email and password required']);
            return;
        }
        
        $result = $this->auth->login($data['email'], $data['password']);
        
        http_response_code($result['success'] ? 200 : 401);
        echo json_encode($result);
    }
    
    private function logout() {
        $result = $this->auth->logout();
        echo json_encode($result);
    }
    
    private function getCurrentUser() {
        if (!$this->auth->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['message' => 'Not authenticated']);
            return;
        }
        
        $user = $this->auth->getCurrentUser();
        unset($user['password']); // Remove sensitive data
        
        echo json_encode(['user' => $user]);
    }
    
    // Helper methods
    private function notFound() {
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
    }
    
    private function methodNotAllowed() {
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
    }
    
    protected function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    protected function sendResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
    }
    
    protected function sendError($message, $status = 400) {
        http_response_code($status);
        echo json_encode(['message' => $message]);
    }
}
?>