<?php
$title = 'Dashboard - SPBU Management System';
$currentPage = 'dashboard';
ob_start();
?>

<h2>Dashboard</h2>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $stats['total_users'] ?? 0 ?></h3>
        <p>Total Karyawan</p>
    </div>
    
    <div class="stat-card">
        <h3><?= $stats['total_stores'] ?? 0 ?></h3>
        <p>Total Toko</p>
    </div>
    
    <div class="stat-card">
        <h3><?= $stats['today_attendance'] ?? 0 ?></h3>
        <p>Absensi Hari Ini</p>
    </div>
    
    <div class="stat-card">
        <h3>Rp <?= number_format($stats['today_sales'] ?? 0, 0, ',', '.') ?></h3>
        <p>Penjualan Hari Ini</p>
    </div>
</div>

<div class="card">
    <h3>Aktivitas Terbaru</h3>
    
    <?php if (empty($recent_activities)): ?>
        <p style="color: #666; text-align: center; padding: 2rem;">Belum ada aktivitas hari ini.</p>
    <?php else: ?>
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Karyawan</th>
                        <th>Aktivitas</th>
                        <th>Toko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_activities as $activity): ?>
                        <tr>
                            <td><?= date('H:i', strtotime($activity['created_at'] ?? $activity['date'])) ?></td>
                            <td><?= htmlspecialchars($activity['employee_name']) ?></td>
                            <td><?= htmlspecialchars($activity['activity']) ?></td>
                            <td><?= htmlspecialchars($activity['store_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="/attendance/new" class="btn">Catat Absensi</a>
        <a href="/sales/new" class="btn">Input Penjualan</a>
        <a href="/proposals/new" class="btn">Buat Saran</a>
        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
            <a href="/users/new" class="btn">Tambah Karyawan</a>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>