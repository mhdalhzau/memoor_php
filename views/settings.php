<?php
$title = 'System Settings';
$currentPage = 'settings';
ob_start();
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i data-feather="settings" class="me-2"></i>
                    Pengaturan Sistem SPBU
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Store Management Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="home" class="me-2"></i>
                    Manajemen Toko
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h6 class="mb-3">Konfigurasi Toko</h6>
                        <form method="POST" action="/settings/store">
                            <div class="mb-3">
                                <label class="form-label small" for="default_store">Default Store</label>
                                <select class="form-select" id="default_store" name="default_store">
                                    <?php foreach ($stores as $store): ?>
                                        <option value="<?= $store['id'] ?>" <?= ($settings['default_store'] ?? 1) == $store['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($store['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small" for="auto_backup">Auto Backup</label>
                                <select class="form-select" id="auto_backup" name="auto_backup">
                                    <option value="daily" <?= ($settings['auto_backup'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>Harian</option>
                                    <option value="weekly" <?= ($settings['auto_backup'] ?? 'daily') === 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                                    <option value="monthly" <?= ($settings['auto_backup'] ?? 'daily') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="feather-xs me-1"></i>
                                Simpan Pengaturan
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-lg-6">
                        <h6 class="mb-3">Status Toko</h6>
                        <div class="list-group">
                            <?php foreach ($stores as $store): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="home" class="feather-sm me-2"></i>
                                        <?= htmlspecialchars($store['name']) ?>
                                    </div>
                                    <div class="badge bg-<?= ($store['status'] ?? 'active') === 'active' ? 'success' : 'danger' ?>-soft text-<?= ($store['status'] ?? 'active') === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($store['status'] ?? 'active') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <a href="/stores" class="btn btn-outline-primary w-100 mt-3">
                            <i data-feather="external-link" class="feather-xs me-1"></i>
                            Kelola Semua Toko
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Management Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="users" class="me-2"></i>
                    Manajemen User
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h6 class="mb-3">Permission User</h6>
                        <form method="POST" action="/settings/users">
                            <div class="mb-3">
                                <label class="form-label small" for="staff_can_edit_sales">Staff dapat edit penjualan</label>
                                <select class="form-select" id="staff_can_edit_sales" name="staff_can_edit_sales">
                                    <option value="1" <?= ($settings['staff_can_edit_sales'] ?? 0) ? 'selected' : '' ?>>Ya</option>
                                    <option value="0" <?= !($settings['staff_can_edit_sales'] ?? 0) ? 'selected' : '' ?>>Tidak</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small" for="require_approval">Approval untuk lembur</label>
                                <select class="form-select" id="require_approval" name="require_approval">
                                    <option value="1" <?= ($settings['require_approval'] ?? 1) ? 'selected' : '' ?>>Ya</option>
                                    <option value="0" <?= !($settings['require_approval'] ?? 1) ? 'selected' : '' ?>>Tidak</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small" for="session_timeout">Session Timeout (menit)</label>
                                <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="<?= $settings['session_timeout'] ?? 120 ?>" min="30" max="480">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="feather-xs me-1"></i>
                                Simpan Pengaturan
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-lg-6">
                        <h6 class="mb-3">Statistik User</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card bg-primary-soft border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="text-primary">
                                            <i data-feather="users" class="mb-2"></i>
                                            <div class="h4 mb-0"><?= count($users_stats['all']) ?></div>
                                            <div class="small">Total Karyawan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-success-soft border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="text-success">
                                            <i data-feather="user-check" class="mb-2"></i>
                                            <div class="h4 mb-0"><?= count($users_stats['managers']) ?></div>
                                            <div class="small">Manager</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-warning-soft border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="text-warning">
                                            <i data-feather="user" class="mb-2"></i>
                                            <div class="h4 mb-0"><?= count($users_stats['staff']) ?></div>
                                            <div class="small">Staff</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-info-soft border-0 h-100">
                                    <div class="card-body text-center">
                                        <div class="text-info">
                                            <i data-feather="shield" class="mb-2"></i>
                                            <div class="h4 mb-0"><?= count($users_stats['admins']) ?></div>
                                            <div class="small">Admin</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <a href="/users" class="btn btn-outline-primary w-100 mt-3">
                            <i data-feather="external-link" class="feather-xs me-1"></i>
                            Kelola Semua User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Settings -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="settings" class="me-2"></i>
                    Pengaturan Umum
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/settings/system">
                    <div class="mb-3">
                        <label class="form-label small" for="app_name">Nama Aplikasi</label>
                        <input type="text" class="form-control" id="app_name" name="app_name" value="<?= $settings['app_name'] ?? 'SPBU Management System' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small" for="timezone">Timezone</label>
                        <select class="form-select" id="timezone" name="timezone">
                            <option value="Asia/Jakarta" <?= ($settings['timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" <?= ($settings['timezone'] ?? 'Asia/Jakarta') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" <?= ($settings['timezone'] ?? 'Asia/Jakarta') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small" for="currency">Currency</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="IDR" <?= ($settings['currency'] ?? 'IDR') === 'IDR' ? 'selected' : '' ?>>IDR (Rupiah)</option>
                            <option value="USD" <?= ($settings['currency'] ?? 'IDR') === 'USD' ? 'selected' : '' ?>>USD (Dollar)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="feather-xs me-1"></i>
                        Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="database" class="me-2"></i>
                    Backup & Maintenance
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Last Backup</span>
                        <small class="text-muted"><?= $system_status['last_backup'] ?? 'Never' ?></small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Database Size</span>
                        <small class="text-muted"><?= $system_status['db_size'] ?? 'Unknown' ?></small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>System Version</span>
                        <div class="badge bg-primary-soft text-primary"><?= $system_status['version'] ?? '1.0.0' ?></div>
                    </div>
                </div>
                
                <div class="mt-3 d-grid gap-2">
                    <button onclick="createBackup()" class="btn btn-outline-primary">
                        <i data-feather="download" class="feather-xs me-1"></i>
                        Create Backup
                    </button>
                    <button onclick="clearCache()" class="btn btn-outline-warning">
                        <i data-feather="refresh-cw" class="feather-xs me-1"></i>
                        Clear Cache
                    </button>
                    <button onclick="viewSystemLogs()" class="btn btn-outline-secondary">
                        <i data-feather="file-text" class="feather-xs me-1"></i>
                        System Logs
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createBackup() {
    if (confirm('Create database backup? This may take a few minutes.')) {
        alert('Backup process started... You will be notified when complete.');
    }
}

function clearCache() {
    if (confirm('Clear application cache? This may affect performance temporarily.')) {
        alert('Cache cleared successfully!');
    }
}

function viewSystemLogs() {
    alert('System logs viewer akan segera tersedia!');
}
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>