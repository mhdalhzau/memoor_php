<?php
$title = 'Dashboard';
$currentPage = 'dashboard';
ob_start();
?>

<!-- Stats Cards Row -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Karyawan</div>
                        <div class="text-lg fw-bold"><?= $stats['total_users'] ?? 0 ?></div>
                    </div>
                    <i data-feather="users" class="feather-xl text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="/users">View Details</a>
                <div class="text-white"><i data-feather="chevron-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Toko</div>
                        <div class="text-lg fw-bold"><?= $stats['total_stores'] ?? 0 ?></div>
                    </div>
                    <i data-feather="home" class="feather-xl text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="/stores">View Details</a>
                <div class="text-white"><i data-feather="chevron-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Absensi Hari Ini</div>
                        <div class="text-lg fw-bold"><?= $stats['today_attendance'] ?? 0 ?></div>
                    </div>
                    <i data-feather="clock" class="feather-xl text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="/attendance">View Details</a>
                <div class="text-white"><i data-feather="chevron-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Penjualan Hari Ini</div>
                        <div class="text-lg fw-bold">Rp <?= number_format($stats['today_sales'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <i data-feather="shopping-cart" class="feather-xl text-white-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="/sales">View Details</a>
                <div class="text-white"><i data-feather="chevron-right"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Activity Row -->
<div class="row mt-4">
    <div class="col-xl-8">
        <!-- Area Chart Example-->
        <div class="card h-100">
            <div class="card-header">Recent Activity</div>
            <div class="card-body">
                <?php if (empty($recent_activities)): ?>
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i data-feather="inbox" class="feather-xl"></i>
                            <p class="mt-3">Belum ada aktivitas hari ini</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($recent_activities, 0, 6) as $activity): ?>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center flex-shrink-0">
                                <div class="avatar avatar-sm me-2">
                                    <img class="avatar-img img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($activity['employee_name']) ?>&background=random" />
                                </div>
                                <div class="d-flex flex-column fw-bold">
                                    <?= htmlspecialchars($activity['employee_name']) ?>
                                    <div class="text-muted"><?= date('H:i', strtotime($activity['created_at'] ?? $activity['date'])) ?></div>
                                </div>
                            </div>
                            <div class="d-flex flex-column text-end">
                                <div class="small"><?= htmlspecialchars($activity['activity']) ?></div>
                                <div class="badge bg-primary-soft text-primary"><?= htmlspecialchars($activity['store_name']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($recent_activities) > 6): ?>
                        <div class="text-center">
                            <a href="/activity" class="btn btn-outline-primary btn-sm">View All Activity</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <!-- Progress Cards-->
        <div class="card h-100">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a class="btn btn-primary" href="/attendance/new">
                        <i data-feather="clock" class="me-1"></i>
                        Catat Absensi
                    </a>
                    <a class="btn btn-outline-primary" href="/sales/new">
                        <i data-feather="shopping-cart" class="me-1"></i>
                        Input Penjualan
                    </a>
                    <a class="btn btn-outline-primary" href="/proposals/new">
                        <i data-feather="message-square" class="me-1"></i>
                        Buat Saran
                    </a>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'staff'): ?>
                        <a class="btn btn-outline-primary" href="/users/new">
                            <i data-feather="user-plus" class="me-1"></i>
                            Tambah Karyawan
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>