<?php
class AttendanceApi {
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
                    $this->getAllAttendance();
                } elseif ($segments[0] === 'user' && isset($segments[1])) {
                    $this->getUserAttendance($segments[1], $segments[2] ?? null);
                } else {
                    $this->getAttendance($segments[0]);
                }
                break;
                
            case 'POST':
                $this->createAttendance();
                break;
                
            case 'PUT':
                if (!empty($segments)) {
                    $this->updateAttendance($segments[0]);
                } else {
                    $this->sendError('Attendance ID required', 400);
                }
                break;
                
            case 'PATCH':
                if (!empty($segments) && $segments[1] === 'approve') {
                    $this->approveAttendance($segments[0]);
                } else {
                    $this->sendError('Invalid patch operation', 400);
                }
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function getAllAttendance() {
        $this->auth->requireAuth();
        
        $storeId = $_GET['storeId'] ?? null;
        $date = $_GET['date'] ?? null;
        $currentUser = $this->auth->getCurrentUser();
        
        try {
            if ($currentUser['role'] === 'staff' && !$storeId) {
                // Staff can see their own attendance
                $stmt = $this->db->prepare("
                    SELECT a.*, u.name as employee_name, u.role as employee_role, s.name as store_name
                    FROM attendance a
                    JOIN users u ON a.user_id = u.id
                    JOIN stores s ON a.store_id = s.id
                    WHERE a.user_id = ?
                    ORDER BY a.date DESC
                ");
                $stmt->execute([$currentUser['id']]);
            } else {
                // Managers and admins can see all attendance
                $this->auth->requireRole(['manager', 'administrasi']);
                
                $sql = "
                    SELECT a.*, u.name as employee_name, u.role as employee_role, s.name as store_name
                    FROM attendance a
                    JOIN users u ON a.user_id = u.id
                    JOIN stores s ON a.store_id = s.id
                ";
                $params = [];
                $conditions = [];
                
                if ($storeId) {
                    $conditions[] = "a.store_id = ?";
                    $params[] = $storeId;
                }
                
                if ($date) {
                    $conditions[] = "DATE(a.date) = ?";
                    $params[] = $date;
                }
                
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                
                $sql .= " ORDER BY a.date DESC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }
            
            $attendance = $stmt->fetchAll();
            $this->sendResponse($attendance);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch attendance: ' . $e->getMessage(), 500);
        }
    }
    
    private function getUserAttendance($userId, $date = null) {
        $this->auth->requireAuth();
        
        $currentUser = $this->auth->getCurrentUser();
        
        // Check permissions
        if ($userId !== $currentUser['id'] && !$this->auth->hasRole(['manager', 'administrasi'])) {
            $this->sendError('Access denied', 403);
            return;
        }
        
        try {
            $sql = "
                SELECT a.*, u.name as employee_name, u.role as employee_role, s.name as store_name
                FROM attendance a
                JOIN users u ON a.user_id = u.id
                JOIN stores s ON a.store_id = s.id
                WHERE a.user_id = ?
            ";
            $params = [$userId];
            
            if ($date) {
                $sql .= " AND DATE(a.date) = ?";
                $params[] = $date;
            }
            
            $sql .= " ORDER BY a.date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $attendance = $stmt->fetchAll();
            
            $this->sendResponse($attendance);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to fetch user attendance: ' . $e->getMessage(), 500);
        }
    }
    
    private function createAttendance() {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data || !isset($data['store_id'], $data['date'])) {
            $this->sendError('Missing required fields: store_id, date', 400);
            return;
        }
        
        // Determine target user - managers can create for others
        $targetUserId = $currentUser['id'];
        if (isset($data['user_id']) && $this->auth->hasRole(['manager', 'administrasi'])) {
            $targetUserId = $data['user_id'];
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO attendance (id, user_id, store_id, date, check_in, check_out, shift, lateness_minutes, overtime_minutes, break_duration, overtime) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $attendanceId = $this->generateUuid();
            
            $stmt->execute([
                $attendanceId,
                $targetUserId,
                $data['store_id'],
                $data['date'],
                $data['check_in'] ?? null,
                $data['check_out'] ?? null,
                $data['shift'] ?? null,
                $data['lateness_minutes'] ?? 0,
                $data['overtime_minutes'] ?? 0,
                $data['break_duration'] ?? 0,
                $data['overtime'] ?? 0.00
            ]);
            
            $this->sendResponse(['message' => 'Attendance created successfully', 'attendance_id' => $attendanceId], 201);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to create attendance: ' . $e->getMessage(), 500);
        }
    }
    
    private function updateAttendance($attendanceId) {
        $this->auth->requireAuth();
        
        $data = $this->getJsonInput();
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$data) {
            $this->sendError('No data provided', 400);
            return;
        }
        
        try {
            // Check if record exists and user has permission
            $stmt = $this->db->prepare("SELECT user_id FROM attendance WHERE id = ?");
            $stmt->execute([$attendanceId]);
            $attendance = $stmt->fetch();
            
            if (!$attendance) {
                $this->sendError('Attendance record not found', 404);
                return;
            }
            
            // Check permissions
            if ($attendance['user_id'] !== $currentUser['id'] && !$this->auth->hasRole(['manager', 'administrasi'])) {
                $this->sendError('Access denied', 403);
                return;
            }
            
            $setParts = [];
            $params = [];
            
            foreach (['check_in', 'check_out', 'shift', 'lateness_minutes', 'overtime_minutes', 'break_duration', 'overtime'] as $field) {
                if (isset($data[$field])) {
                    $setParts[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setParts)) {
                $this->sendError('No valid fields to update', 400);
                return;
            }
            
            $params[] = $attendanceId;
            
            $stmt = $this->db->prepare("
                UPDATE attendance SET " . implode(', ', $setParts) . " WHERE id = ?
            ");
            $stmt->execute($params);
            
            $this->sendResponse(['message' => 'Attendance updated successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to update attendance: ' . $e->getMessage(), 500);
        }
    }
    
    private function approveAttendance($attendanceId) {
        $this->auth->requireRole(['manager', 'administrasi']);
        
        try {
            $stmt = $this->db->prepare("
                UPDATE attendance SET status = 'approved' WHERE id = ?
            ");
            $stmt->execute([$attendanceId]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendError('Attendance record not found', 404);
                return;
            }
            
            $this->sendResponse(['message' => 'Attendance approved successfully']);
            
        } catch (PDOException $e) {
            $this->sendError('Failed to approve attendance: ' . $e->getMessage(), 500);
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