<?php
$title = 'Payroll Management';
$currentPage = 'payroll';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="dollar-sign" class="me-1"></i>
            Filter Data Gaji
        </div>
        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
            <a href="/payroll/new" class="btn btn-primary btn-sm">
                <i data-feather="plus" class="me-1"></i>
                Proses Gaji Baru
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="small mb-1" for="month">Bulan</label>
                <input class="form-control" id="month" name="month" type="month" value="<?= $_GET['month'] ?? date('Y-m') ?>" />
            </div>
            
            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
            <div class="col-md-4">
                <label class="small mb-1" for="store_id">Toko</label>
                <select class="form-select" id="store_id" name="store_id">
                    <option value="">Semua Toko</option>
                    <?php foreach ($stores as $store): ?>
                        <option value="<?= $store['id'] ?>" <?= ($_GET['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($store['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-outline-primary" type="submit">
                    <i data-feather="search" class="me-1"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Card -->
<div class="card">
    <div class="card-header">
        <i data-feather="users" class="me-1"></i>
        Data Gaji Karyawan
    </div>
    <div class="card-body">
        <?php if (empty($payroll_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="calendar" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data gaji untuk bulan yang dipilih.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Toko</th>
                            <th>Bulan</th>
                            <th>Gaji Pokok</th>
                            <th>Kehadiran</th>
                            <th>Lembur</th>
                            <th>Total Gaji</th>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_payroll = 0;
                        foreach ($payroll_list as $payroll): 
                            $overtime_pay = ($payroll['overtime_hours'] ?? 0) * 15000; // Rp 15k per jam
                            $total_pay = $payroll['base_salary'] + $overtime_pay;
                            $total_payroll += $total_pay;
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img class="avatar-img img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($payroll['employee_name']) ?>&background=random" />
                                        </div>
                                        <div class="fw-500"><?= htmlspecialchars($payroll['employee_name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-img rounded-circle bg-success-soft text-success d-flex align-items-center justify-content-center">
                                                <i data-feather="home"></i>
                                            </div>
                                        </div>
                                        <?= htmlspecialchars($payroll['store_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge bg-info-soft text-info">
                                        <?= date('M Y', strtotime($payroll['month'] . '-01')) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        Rp <?= number_format($payroll['base_salary'], 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="badge bg-success-soft text-success">
                                            <?= $payroll['attendance_count'] ?? 0 ?> hari
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <?php if (($payroll['overtime_hours'] ?? 0) > 0): ?>
                                            <div class="badge bg-warning-soft text-warning">
                                                <?= $payroll['overtime_hours'] ?> jam
                                            </div>
                                            <div class="text-muted mt-1">
                                                +Rp <?= number_format($overtime_pay, 0, ',', '.') ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success fs-6">
                                        Rp <?= number_format($total_pay, 0, ',', '.') ?>
                                    </div>
                                </td>
                                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/payroll/edit/<?= $payroll['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                                <a class="dropdown-item" href="/payroll/slip/<?= $payroll['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="file-text"></i>
                                                    </div>
                                                    Slip Gaji
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="6" class="fw-bold">Total Penggajian:</td>
                            <td class="fw-bold text-success fs-5">
                                Rp <?= number_format($total_payroll, 0, ',', '.') ?>
                            </td>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <td></td>
                            <?php endif; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add DataTables JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="/assets/js/datatables-simple.js"></script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>