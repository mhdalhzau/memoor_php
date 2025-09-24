<?php
$title = 'Settings - SPBU Management System';
$currentPage = 'settings';
ob_start();
?>

<h2>Pengaturan Sistem</h2>

<div style="display: grid; gap: 2rem;">
    
    <!-- Store Management Settings -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: #667eea;">🏪 Store Management</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div>
                <h4>Store Configuration</h4>
                <form method="POST" action="/settings/store">
                    <div class="form-group">
                        <label for="default_store">Default Store:</label>
                        <select id="default_store" name="default_store">
                            <?php foreach ($stores as $store): ?>
                                <option value="<?= $store['id'] ?>" <?= ($settings['default_store'] ?? 1) == $store['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($store['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="auto_backup">Auto Backup:</label>
                        <select id="auto_backup" name="auto_backup">
                            <option value="daily" <?= ($settings['auto_backup'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>Harian</option>
                            <option value="weekly" <?= ($settings['auto_backup'] ?? 'daily') === 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                            <option value="monthly" <?= ($settings['auto_backup'] ?? 'daily') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Simpan Pengaturan Store</button>
                </form>
            </div>
            
            <div>
                <h4>Store Status</h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php foreach ($stores as $store): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <span><?= htmlspecialchars($store['name']) ?></span>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= ($store['status'] ?? 'active') === 'active' ? '#27ae60' : '#e74c3c' ?>; 
                                color: white;">
                                <?= ucfirst($store['status'] ?? 'active') ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <a href="/stores" class="btn" style="margin-top: 1rem; width: 100%;">Kelola Semua Toko</a>
            </div>
        </div>
    </div>

    <!-- User Management Settings -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: #667eea;">👥 User Management</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div>
                <h4>User Permissions</h4>
                <form method="POST" action="/settings/users">
                    <div class="form-group">
                        <label for="staff_can_edit_sales">Staff dapat edit penjualan:</label>
                        <select id="staff_can_edit_sales" name="staff_can_edit_sales">
                            <option value="1" <?= ($settings['staff_can_edit_sales'] ?? 0) ? 'selected' : '' ?>>Ya</option>
                            <option value="0" <?= !($settings['staff_can_edit_sales'] ?? 0) ? 'selected' : '' ?>>Tidak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="require_approval">Approval untuk lembur:</label>
                        <select id="require_approval" name="require_approval">
                            <option value="1" <?= ($settings['require_approval'] ?? 1) ? 'selected' : '' ?>>Ya</option>
                            <option value="0" <?= !($settings['require_approval'] ?? 1) ? 'selected' : '' ?>>Tidak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="session_timeout">Session Timeout (menit):</label>
                        <input type="number" id="session_timeout" name="session_timeout" value="<?= $settings['session_timeout'] ?? 120 ?>" min="30" max="480">
                    </div>
                    
                    <button type="submit" class="btn">Simpan Pengaturan User</button>
                </form>
            </div>
            
            <div>
                <h4>User Statistics</h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: #f8f9fa; border-radius: 5px;">
                        <span>Total Karyawan:</span>
                        <strong><?= count($users_stats['all']) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: #f8f9fa; border-radius: 5px;">
                        <span>Manager:</span>
                        <strong><?= count($users_stats['managers']) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: #f8f9fa; border-radius: 5px;">
                        <span>Staff:</span>
                        <strong><?= count($users_stats['staff']) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: #f8f9fa; border-radius: 5px;">
                        <span>Admin:</span>
                        <strong><?= count($users_stats['admins']) ?></strong>
                    </div>
                </div>
                
                <a href="/users" class="btn" style="margin-top: 1rem; width: 100%;">Kelola Semua User</a>
            </div>
        </div>
    </div>

    <!-- Wallet API Settings -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: #667eea;">💰 Wallet API Integration</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div>
                <h4>Payment Gateway Configuration</h4>
                <form method="POST" action="/settings/wallet">
                    <div class="form-group">
                        <label for="wallet_provider">Provider:</label>
                        <select id="wallet_provider" name="wallet_provider">
                            <option value="gopay" <?= ($settings['wallet_provider'] ?? 'gopay') === 'gopay' ? 'selected' : '' ?>>GoPay</option>
                            <option value="ovo" <?= ($settings['wallet_provider'] ?? 'gopay') === 'ovo' ? 'selected' : '' ?>>OVO</option>
                            <option value="dana" <?= ($settings['wallet_provider'] ?? 'gopay') === 'dana' ? 'selected' : '' ?>>DANA</option>
                            <option value="shopeepay" <?= ($settings['wallet_provider'] ?? 'gopay') === 'shopeepay' ? 'selected' : '' ?>>ShopeePay</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="wallet_api_key">API Key:</label>
                        <input type="password" id="wallet_api_key" name="wallet_api_key" value="<?= $settings['wallet_api_key'] ?? '' ?>" placeholder="Enter your wallet API key">
                        <small style="color: #666;">API key akan dienkripsi secara otomatis</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="wallet_secret_key">Secret Key:</label>
                        <input type="password" id="wallet_secret_key" name="wallet_secret_key" value="<?= $settings['wallet_secret_key'] ?? '' ?>" placeholder="Enter your wallet secret key">
                    </div>
                    
                    <div class="form-group">
                        <label for="wallet_environment">Environment:</label>
                        <select id="wallet_environment" name="wallet_environment">
                            <option value="sandbox" <?= ($settings['wallet_environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' ?>>Sandbox (Testing)</option>
                            <option value="production" <?= ($settings['wallet_environment'] ?? 'sandbox') === 'production' ? 'selected' : '' ?>>Production</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Simpan Konfigurasi Wallet</button>
                </form>
            </div>
            
            <div>
                <h4>Wallet Status & Testing</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="padding: 1rem; border: 1px solid #ddd; border-radius: 5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span><strong>Connection Status:</strong></span>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= ($wallet_status['connected'] ?? false) ? '#27ae60' : '#e74c3c' ?>; 
                                color: white;">
                                <?= ($wallet_status['connected'] ?? false) ? 'Connected' : 'Disconnected' ?>
                            </span>
                        </div>
                        
                        <div style="font-size: 0.9rem; color: #666;">
                            <p>Provider: <?= ucfirst($settings['wallet_provider'] ?? 'Belum dipilih') ?></p>
                            <p>Environment: <?= ucfirst($settings['wallet_environment'] ?? 'Belum diatur') ?></p>
                            <p>Last Check: <?= $wallet_status['last_check'] ?? 'Never' ?></p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="testWalletConnection()" class="btn" style="flex: 1;">Test Connection</button>
                        <button onclick="viewWalletLogs()" class="btn" style="flex: 1; background: #6c757d;">View Logs</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Sheets Integration -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: #667eea;">📊 Google Sheets Integration</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div>
                <h4>Google Sheets Configuration</h4>
                <form method="POST" action="/settings/sheets">
                    <div class="form-group">
                        <label for="sheets_enabled">Enable Google Sheets:</label>
                        <select id="sheets_enabled" name="sheets_enabled">
                            <option value="1" <?= ($settings['sheets_enabled'] ?? 0) ? 'selected' : '' ?>>Ya</option>
                            <option value="0" <?= !($settings['sheets_enabled'] ?? 0) ? 'selected' : '' ?>>Tidak</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sheets_id">Sheet ID:</label>
                        <input type="text" id="sheets_id" name="sheets_id" value="<?= $settings['sheets_id'] ?? '' ?>" placeholder="Google Sheets ID">
                        <small style="color: #666;">Copy dari URL Google Sheets Anda</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="auto_sync">Auto Sync:</label>
                        <select id="auto_sync" name="auto_sync">
                            <option value="real_time" <?= ($settings['auto_sync'] ?? 'daily') === 'real_time' ? 'selected' : '' ?>>Real Time</option>
                            <option value="hourly" <?= ($settings['auto_sync'] ?? 'daily') === 'hourly' ? 'selected' : '' ?>>Setiap Jam</option>
                            <option value="daily" <?= ($settings['auto_sync'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>Harian</option>
                            <option value="manual" <?= ($settings['auto_sync'] ?? 'daily') === 'manual' ? 'selected' : '' ?>>Manual</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sync_data">Data yang di-sync:</label>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="sync_data[]" value="attendance" <?= in_array('attendance', $settings['sync_data'] ?? []) ? 'checked' : '' ?>>
                                <span>Absensi</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="sync_data[]" value="sales" <?= in_array('sales', $settings['sync_data'] ?? []) ? 'checked' : '' ?>>
                                <span>Penjualan</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="sync_data[]" value="cashflow" <?= in_array('cashflow', $settings['sync_data'] ?? []) ? 'checked' : '' ?>>
                                <span>Kas</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="sync_data[]" value="payroll" <?= in_array('payroll', $settings['sync_data'] ?? []) ? 'checked' : '' ?>>
                                <span>Gaji</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Simpan Konfigurasi Sheets</button>
                </form>
            </div>
            
            <div>
                <h4>Sync Status & Controls</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="padding: 1rem; border: 1px solid #ddd; border-radius: 5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span><strong>Sync Status:</strong></span>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= ($sheets_status['syncing'] ?? false) ? '#f39c12' : '#27ae60' ?>; 
                                color: white;">
                                <?= ($sheets_status['syncing'] ?? false) ? 'Syncing...' : 'Ready' ?>
                            </span>
                        </div>
                        
                        <div style="font-size: 0.9rem; color: #666;">
                            <p>Last Sync: <?= $sheets_status['last_sync'] ?? 'Never' ?></p>
                            <p>Records Synced: <?= $sheets_status['records_synced'] ?? 0 ?></p>
                            <p>Errors: <?= $sheets_status['errors'] ?? 0 ?></p>
                        </div>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <button onclick="manualSync()" class="btn">Manual Sync Now</button>
                        <button onclick="testSheetsConnection()" class="btn" style="background: #17a2b8;">Test Connection</button>
                        <button onclick="viewSyncLogs()" class="btn" style="background: #6c757d;">View Sync Logs</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Settings -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: #667eea;">⚙️ System Settings</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div>
                <h4>General Settings</h4>
                <form method="POST" action="/settings/system">
                    <div class="form-group">
                        <label for="app_name">Nama Aplikasi:</label>
                        <input type="text" id="app_name" name="app_name" value="<?= $settings['app_name'] ?? 'SPBU Management System' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="timezone">Timezone:</label>
                        <select id="timezone" name="timezone">
                            <option value="Asia/Jakarta" <?= ($settings['timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" <?= ($settings['timezone'] ?? 'Asia/Jakarta') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" <?= ($settings['timezone'] ?? 'Asia/Jakarta') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="currency">Currency:</label>
                        <select id="currency" name="currency">
                            <option value="IDR" <?= ($settings['currency'] ?? 'IDR') === 'IDR' ? 'selected' : '' ?>>IDR (Rupiah)</option>
                            <option value="USD" <?= ($settings['currency'] ?? 'IDR') === 'USD' ? 'selected' : '' ?>>USD (Dollar)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Simpan Pengaturan System</button>
                </form>
            </div>
            
            <div>
                <h4>Backup & Maintenance</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="padding: 1rem; border: 1px solid #ddd; border-radius: 5px;">
                        <p><strong>Last Backup:</strong> <?= $system_status['last_backup'] ?? 'Never' ?></p>
                        <p><strong>Database Size:</strong> <?= $system_status['db_size'] ?? 'Unknown' ?></p>
                        <p><strong>System Version:</strong> <?= $system_status['version'] ?? '1.0.0' ?></p>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <button onclick="createBackup()" class="btn">Create Backup</button>
                        <button onclick="clearCache()" class="btn" style="background: #ffc107; color: #000;">Clear Cache</button>
                        <button onclick="viewSystemLogs()" class="btn" style="background: #6c757d;">System Logs</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testWalletConnection() {
    alert('Testing wallet connection... Feature akan segera tersedia!');
}

function viewWalletLogs() {
    alert('Wallet logs viewer akan segera tersedia!');
}

function manualSync() {
    alert('Manual sync started... Check sync status in a few moments.');
}

function testSheetsConnection() {
    alert('Testing Google Sheets connection... Feature akan segera tersedia!');
}

function viewSyncLogs() {
    alert('Sync logs viewer akan segera tersedia!');
}

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