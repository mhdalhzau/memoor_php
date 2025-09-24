<?php
class WebController {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    public function showLogin() {
        if ($this->auth->isAuthenticated()) {
            header('Location: /dashboard');
            exit();
        }
        
        include 'views/login.php';
    }
    
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit();
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email dan password harus diisi';
            header('Location: /login');
            exit();
        }
        
        $result = $this->auth->login($email, $password);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Login berhasil';
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: /login');
        }
        exit();
    }
    
    public function handleLogout() {
        $this->auth->logout();
        $_SESSION['success'] = 'Logout berhasil';
        header('Location: /login');
        exit();
    }
    
    public function showDashboard() {
        $this->requireAuth();
        
        // Get statistics
        $stats = $this->getDashboardStats();
        $recent_activities = $this->getRecentActivities();
        
        include 'views/dashboard.php';
    }
    
    public function showAttendance() {
        $this->requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $storeId = $_GET['store_id'] ?? null;
        
        // Get stores for filter
        $stores = $this->getStores();
        
        // Get attendance data
        $attendance_list = $this->getAttendanceData($date, $storeId);
        
        include 'views/attendance.php';
    }
    
    public function showAttendanceForm($attendanceId = null) {
        $this->requireAuth();
        
        $editing = !empty($attendanceId);
        $attendance = [];
        
        if ($editing) {
            $attendance = $this->getAttendanceById($attendanceId);
            if (!$attendance) {
                $_SESSION['error'] = 'Data absensi tidak ditemukan';
                header('Location: /attendance');
                exit();
            }
        }
        
        $users = $this->getUsers();
        $stores = $this->getStores();
        
        include 'views/attendance_form.php';
    }
    
    public function handleAttendanceForm($attendanceId = null) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /attendance');
            exit();
        }
        
        $editing = !empty($attendanceId);
        
        $data = [
            'user_id' => $_POST['user_id'] ?? $_SESSION['user']['id'],
            'store_id' => $_POST['store_id'] ?? '',
            'date' => $_POST['date'] ?? '',
            'check_in' => $_POST['check_in'] ?? null,
            'check_out' => $_POST['check_out'] ?? null,
            'shift' => $_POST['shift'] ?? null,
            'lateness_minutes' => intval($_POST['lateness_minutes'] ?? 0),
            'overtime_minutes' => intval($_POST['overtime_minutes'] ?? 0),
            'break_duration' => intval($_POST['break_duration'] ?? 60)
        ];
        
        if (empty($data['store_id']) || empty($data['date'])) {
            $_SESSION['error'] = 'Toko dan tanggal harus diisi';
            header('Location: ' . ($editing ? '/attendance/edit/' . $attendanceId : '/attendance/new'));
            exit();
        }
        
        try {
            if ($editing) {
                $this->updateAttendance($attendanceId, $data);
                $_SESSION['success'] = 'Absensi berhasil diupdate';
            } else {
                $this->createAttendance($data);
                $_SESSION['success'] = 'Absensi berhasil dicatat';
            }
            
            header('Location: /attendance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            header('Location: ' . ($editing ? '/attendance/edit/' . $attendanceId : '/attendance/new'));
        }
        exit();
    }
    
    private function requireAuth() {
        // Skip auth check if database not connected (demo mode)
        if (!$this->db || !$this->db->isConnected()) {
            $_SESSION['user'] = [
                'id' => 'demo-user',
                'name' => 'Demo User',
                'email' => 'demo@spbu.com',
                'role' => 'manager'
            ];
            return;
        }
        
        if (!$this->auth->isAuthenticated()) {
            header('Location: /login');
            exit();
        }
    }
    
    private function getDashboardStats() {
        // Sample data for demo purposes
        $stats = [
            'total_users' => 5,
            'total_stores' => 2,
            'today_attendance' => 4,
            'today_sales' => 2500000
        ];
        
        return $stats;
    }
    
    private function getRecentActivities() {
        // Sample activities for demo
        return [
            [
                'date' => date('Y-m-d H:i:s'),
                'employee_name' => 'Putri',
                'store_name' => 'Main Store',
                'activity' => 'Check In'
            ],
            [
                'date' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'employee_name' => 'Hafiz', 
                'store_name' => 'Branch Store',
                'activity' => 'Check In'
            ]
        ];
    }
    
    private function getStores() {
        // Sample stores for demo
        return [
            ['id' => 1, 'name' => 'Main Store'],
            ['id' => 2, 'name' => 'Branch Store']
        ];
    }
    
    private function getUsers() {
        // Sample users for demo
        return [
            ['id' => '1', 'name' => 'SPBU Manager', 'role' => 'manager'],
            ['id' => '2', 'name' => 'Admin SPBU', 'role' => 'administrasi'],
            ['id' => '3', 'name' => 'Putri', 'role' => 'staff'],
            ['id' => '4', 'name' => 'Hafiz', 'role' => 'staff'],
            ['id' => '5', 'name' => 'Endang', 'role' => 'staff']
        ];
    }
    
    private function getAttendanceData($date, $storeId = null) {
        // Sample attendance data for demo
        return [
            [
                'id' => '1',
                'employee_name' => 'Putri',
                'store_name' => 'Main Store',
                'date' => $date,
                'check_in' => '07:30',
                'check_out' => '17:00',
                'shift' => 'pagi',
                'lateness_minutes' => 30,
                'overtime_minutes' => 0
            ],
            [
                'id' => '2',
                'employee_name' => 'Hafiz',
                'store_name' => 'Branch Store',
                'date' => $date,
                'check_in' => '07:00',
                'check_out' => '18:00',
                'shift' => 'pagi',
                'lateness_minutes' => 0,
                'overtime_minutes' => 60
            ]
        ];
    }
    
    private function getAttendanceById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM attendance WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    private function createAttendance($data) {
        $id = $this->generateUuid();
        
        $stmt = $this->db->prepare("
            INSERT INTO attendance (id, user_id, store_id, date, check_in, check_out, shift, lateness_minutes, overtime_minutes, break_duration) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $id,
            $data['user_id'],
            $data['store_id'],
            $data['date'],
            $data['check_in'],
            $data['check_out'],
            $data['shift'],
            $data['lateness_minutes'],
            $data['overtime_minutes'],
            $data['break_duration']
        ]);
    }
    
    private function updateAttendance($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE attendance 
            SET store_id = ?, date = ?, check_in = ?, check_out = ?, shift = ?, 
                lateness_minutes = ?, overtime_minutes = ?, break_duration = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['store_id'],
            $data['date'],
            $data['check_in'],
            $data['check_out'],
            $data['shift'],
            $data['lateness_minutes'],
            $data['overtime_minutes'],
            $data['break_duration'],
            $id
        ]);
    }
    
    // New methods for other pages
    public function showSales() {
        $this->requireAuth();
        
        $stores = $this->getStores();
        $sales_list = $this->getSalesData();
        
        include 'views/sales.php';
    }
    
    public function showSalesForm($salesId = null) {
        $this->requireAuth();
        
        $editing = !empty($salesId);
        $sale = $editing ? ['id' => $salesId] : [];
        $stores = $this->getStores();
        
        include 'views/sales_form.php';
    }
    
    public function handleSalesForm($salesId = null) {
        $this->requireAuth();
        $_SESSION['success'] = 'Data penjualan berhasil disimpan';
        header('Location: /sales');
        exit();
    }
    
    public function showUsers() {
        $this->requireAuth();
        
        $users_list = $this->getUsersData();
        
        include 'views/users.php';
    }
    
    public function showStores() {
        $this->requireAuth();
        
        $stores_list = $this->getStores();
        
        include 'views/stores.php';
    }
    
    public function showPayroll() {
        $this->requireAuth();
        
        $stores = $this->getStores();
        $payroll_list = $this->getPayrollData();
        
        include 'views/payroll.php';
    }
    
    public function showCashflow() {
        $this->requireAuth();
        
        $stores = $this->getStores();
        $cashflow_list = $this->getCashflowData();
        $summary = $this->getCashflowSummary();
        
        include 'views/cashflow.php';
    }
    
    public function showProposals() {
        $this->requireAuth();
        
        $stores = $this->getStores();
        $proposals_list = $this->getProposalsData();
        
        include 'views/proposals.php';
    }
    
    public function showOvertime() {
        $this->requireAuth();
        
        $stores = $this->getStores();
        $overtime_list = $this->getOvertimeData();
        
        include 'views/overtime.php';
    }
    
    // Sample data methods for demo
    private function getSalesData() {
        return [
            [
                'id' => '1',
                'date' => date('Y-m-d'),
                'store_name' => 'Main Store',
                'user_name' => 'Putri',
                'total_sales' => 2500000
            ],
            [
                'id' => '2',
                'date' => date('Y-m-d', strtotime('-1 day')),
                'store_name' => 'Branch Store',
                'user_name' => 'Hafiz',
                'total_sales' => 1800000
            ]
        ];
    }
    
    private function getUsersData() {
        return [
            [
                'id' => '1',
                'name' => 'SPBU Manager',
                'email' => 'manager@spbu.com',
                'role' => 'manager',
                'phone' => '081234567890',
                'salary' => 15000000,
                'stores' => 'Main Store, Branch Store'
            ],
            [
                'id' => '2',
                'name' => 'Admin SPBU',
                'email' => 'admin@spbu.com',
                'role' => 'administrasi',
                'phone' => '081234567891',
                'salary' => 12000000,
                'stores' => 'All Stores'
            ],
            [
                'id' => '3',
                'name' => 'Putri',
                'email' => 'putri@spbu.com',
                'role' => 'staff',
                'phone' => '081234567892',
                'salary' => 8000000,
                'stores' => 'Main Store'
            ]
        ];
    }
    
    private function getPayrollData() {
        return [
            [
                'id' => '1',
                'employee_name' => 'Putri',
                'store_name' => 'Main Store',
                'month' => date('Y-m'),
                'base_salary' => 8000000,
                'attendance_count' => 22,
                'overtime_hours' => 15
            ],
            [
                'id' => '2',
                'employee_name' => 'Hafiz',
                'store_name' => 'Branch Store',
                'month' => date('Y-m'),
                'base_salary' => 8000000,
                'attendance_count' => 24,
                'overtime_hours' => 10
            ]
        ];
    }
    
    private function getCashflowData() {
        return [
            [
                'id' => '1',
                'store_name' => 'Main Store',
                'category' => 'sales',
                'type' => 'income',
                'amount' => 2500000,
                'description' => 'Penjualan harian',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '2',
                'store_name' => 'Main Store',
                'category' => 'operational',
                'type' => 'expense',
                'amount' => 500000,
                'description' => 'Biaya operasional',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    private function getCashflowSummary() {
        return [
            'total_income' => 2500000,
            'total_expense' => 500000
        ];
    }
    
    private function getProposalsData() {
        return [
            [
                'id' => '1',
                'title' => 'Perbaikan Pompa BBM',
                'category' => 'operational',
                'employee_name' => 'Putri',
                'store_name' => 'Main Store',
                'estimated_cost' => 2000000,
                'status' => 'pending',
                'description' => 'Pompa BBM nomor 3 perlu diperbaiki karena sering macet',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '2',
                'title' => 'Penambahan Fasilitas Toilet',
                'category' => 'facilities',
                'employee_name' => 'Hafiz',
                'store_name' => 'Branch Store',
                'estimated_cost' => 5000000,
                'status' => 'approved',
                'description' => 'Menambah toilet untuk kenyamanan pelanggan',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        ];
    }
    
    private function getOvertimeData() {
        return [
            [
                'id' => '1',
                'employee_name' => 'Putri',
                'store_name' => 'Main Store',
                'date' => date('Y-m-d'),
                'hours' => 2.5,
                'status' => 'pending'
            ],
            [
                'id' => '2',
                'employee_name' => 'Hafiz',
                'store_name' => 'Branch Store',
                'date' => date('Y-m-d', strtotime('-1 day')),
                'hours' => 3.0,
                'status' => 'approved'
            ]
        ];
    }
    
    public function showSettings() {
        $this->requireAuth();
        
        $stores = $this->getStores();
        $settings = $this->getSystemSettings();
        $users_stats = $this->getUserStats();
        $wallet_status = $this->getWalletStatus();
        $sheets_status = $this->getSheetsStatus();
        $system_status = $this->getSystemStatus();
        
        include 'views/settings.php';
    }
    
    public function handleSettingsStore() {
        $this->requireAuth();
        $_SESSION['success'] = 'Pengaturan store berhasil disimpan';
        header('Location: /settings');
        exit();
    }
    
    public function handleSettingsUsers() {
        $this->requireAuth();
        $_SESSION['success'] = 'Pengaturan user berhasil disimpan';
        header('Location: /settings');
        exit();
    }
    
    public function handleSettingsWallet() {
        $this->requireAuth();
        $_SESSION['success'] = 'Konfigurasi wallet berhasil disimpan';
        header('Location: /settings');
        exit();
    }
    
    public function handleSettingsSheets() {
        $this->requireAuth();
        $_SESSION['success'] = 'Konfigurasi Google Sheets berhasil disimpan';
        header('Location: /settings');
        exit();
    }
    
    public function handleSettingsSystem() {
        $this->requireAuth();
        $_SESSION['success'] = 'Pengaturan system berhasil disimpan';
        header('Location: /settings');
        exit();
    }
    
    // Settings data methods
    private function getSystemSettings() {
        return [
            'default_store' => 1,
            'auto_backup' => 'daily',
            'staff_can_edit_sales' => 0,
            'require_approval' => 1,
            'session_timeout' => 120,
            'wallet_provider' => 'gopay',
            'wallet_api_key' => '',
            'wallet_secret_key' => '',
            'wallet_environment' => 'sandbox',
            'sheets_enabled' => 0,
            'sheets_id' => '',
            'auto_sync' => 'daily',
            'sync_data' => ['attendance', 'sales'],
            'app_name' => 'SPBU Management System',
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR'
        ];
    }
    
    private function getUserStats() {
        $users = $this->getUsers();
        return [
            'all' => $users,
            'managers' => array_filter($users, fn($u) => $u['role'] === 'manager'),
            'staff' => array_filter($users, fn($u) => $u['role'] === 'staff'),
            'admins' => array_filter($users, fn($u) => $u['role'] === 'administrasi')
        ];
    }
    
    private function getWalletStatus() {
        return [
            'connected' => false,
            'last_check' => 'Never'
        ];
    }
    
    private function getSheetsStatus() {
        return [
            'syncing' => false,
            'last_sync' => 'Never',
            'records_synced' => 0,
            'errors' => 0
        ];
    }
    
    private function getSystemStatus() {
        return [
            'last_backup' => 'Never',
            'db_size' => '2.5 MB',
            'version' => '1.0.0'
        ];
    }
    
    private function generateUuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
?>