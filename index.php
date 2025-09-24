<?php
require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';
require_once 'includes/router.php';
require_once 'controllers/WebController.php';

// Session configuration is already handled in config.php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize authentication
$auth = new Auth($db);

// Initialize controllers
$router = new Router($db, $auth);
$webController = new WebController($database, $auth);

// Get the request method and URI with fallbacks for CLI mode
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Handle static assets first
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $uri)) {
    $filePath = '.' . $uri;
    if (file_exists($filePath)) {
        // Get the file extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Set appropriate content type
        switch ($extension) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: application/javascript');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'gif':
                header('Content-Type: image/gif');
                break;
            case 'ico':
                header('Content-Type: image/x-icon');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
        }
        
        // Send cache headers for better performance
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        exit('File not found');
    }
}

// Remove base path if running in subdirectory
$basePath = '/';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Handle web routes
$uri = trim($uri, '/');
$segments = explode('/', $uri);

try {
    // API routes
    if ($segments[0] === 'api') {
        header('Content-Type: application/json');
        $router->route($method, $uri);
        exit();
    }
    
    // Web routes
    switch ($uri) {
        case '':
        case 'login':
            if ($method === 'GET') {
                $webController->showLogin();
            } else {
                $webController->handleLogin();
            }
            break;
            
        case 'logout':
            $webController->handleLogout();
            break;
            
        case 'dashboard':
            $webController->showDashboard();
            break;
            
        case 'attendance':
            $webController->showAttendance();
            break;
            
        case 'attendance/new':
            if ($method === 'GET') {
                $webController->showAttendanceForm();
            } else {
                $webController->handleAttendanceForm();
            }
            break;
            
        case 'sales':
            $webController->showSales();
            break;
            
        case 'sales/new':
            if ($method === 'GET') {
                $webController->showSalesForm();
            } else {
                $webController->handleSalesForm();
            }
            break;
            
        case 'users':
            $webController->showUsers();
            break;
            
        case 'stores':
            $webController->showStores();
            break;
            
        case 'payroll':
            $webController->showPayroll();
            break;
            
        case 'cashflow':
            $webController->showCashflow();
            break;
            
        case 'proposals':
            $webController->showProposals();
            break;
            
        case 'overtime':
            $webController->showOvertime();
            break;
            
        case 'settings':
            $webController->showSettings();
            break;
            
        default:
            // Handle dynamic routes
            if (preg_match('#^attendance/edit/(.+)$#', $uri, $matches)) {
                if ($method === 'GET') {
                    $webController->showAttendanceForm($matches[1]);
                } else {
                    $webController->handleAttendanceForm($matches[1]);
                }
            } elseif (preg_match('#^sales/edit/(.+)$#', $uri, $matches)) {
                if ($method === 'GET') {
                    $webController->showSalesForm($matches[1]);
                } else {
                    $webController->handleSalesForm($matches[1]);
                }
            } elseif (preg_match('#^stores/edit/(.+)$#', $uri, $matches)) {
                if ($method === 'GET') {
                    $webController->showStoresForm($matches[1]);
                } else {
                    $webController->handleStoresForm($matches[1]);
                }
            } elseif (preg_match('#^settings/(.+)$#', $uri, $matches)) {
                if ($method === 'POST') {
                    switch ($matches[1]) {
                        case 'store':
                            $webController->handleSettingsStore();
                            break;
                        case 'users':
                            $webController->handleSettingsUsers();
                            break;
                        case 'wallet':
                            $webController->handleSettingsWallet();
                            break;
                        case 'sheets':
                            $webController->handleSettingsSheets();
                            break;
                        case 'system':
                            $webController->handleSettingsSystem();
                            break;
                        default:
                            http_response_code(404);
                            echo '<h1>404 - Halaman tidak ditemukan</h1>';
                    }
                } else {
                    http_response_code(404);
                    echo '<h1>404 - Halaman tidak ditemukan</h1>';
                }
            } else {
                http_response_code(404);
                echo '<h1>404 - Halaman tidak ditemukan</h1>';
            }
            break;
    }
    
} catch (Exception $e) {
    if (strpos($uri, 'api') === 0) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['message' => 'Internal server error: ' . $e->getMessage()]);
    } else {
        echo '<h1>500 - Server Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>