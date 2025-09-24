<?php
$title = 'Overtime Management';
$currentPage = 'overtime';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="clock" class="me-1"></i>
            Filter Data Lembur
        </div>
        <a href="/overtime/new" class="btn btn-primary btn-sm">
            <i data-feather="plus" class="me-1"></i>
            Input Lembur Baru
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="small mb-1" for="date">Tanggal</label>
                <input class="form-control" id="date" name="date" type="date" value="<?= $_GET['date'] ?? date('Y-m-d') ?>" />
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
        <i data-feather="trending-up" class="me-1"></i>
        Manajemen Lembur Karyawan
    </div>
    <div class="card-body">
        <?php if (empty($overtime_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="clock" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data lembur untuk tanggal yang dipilih.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Toko</th>
                            <th>Tanggal</th>
                            <th>Jam Lembur</th>
                            <th>Upah Lembur</th>
                            <th>Status</th>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_hours = 0;
                        $total_pay = 0;
                        foreach ($overtime_list as $overtime): 
                            $hourly_rate = 15000; // Rp 15k per jam
                            $overtime_pay = $overtime['hours'] * $hourly_rate;
                            $total_hours += $overtime['hours'];
                            $total_pay += $overtime_pay;
                        ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img class="avatar-img img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($overtime['employee_name']) ?>&background=random" />
                                        </div>
                                        <div class="fw-500"><?= htmlspecialchars($overtime['employee_name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-img rounded-circle bg-primary-soft text-primary d-flex align-items-center justify-content-center">
                                                <i data-feather="home"></i>
                                            </div>
                                        </div>
                                        <?= htmlspecialchars($overtime['store_name']) ?>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($overtime['date'])) ?></td>
                                <td>
                                    <div class="badge bg-warning-soft text-warning">
                                        <i data-feather="clock" class="feather-xs me-1"></i>
                                        <?= number_format($overtime['hours'], 1) ?> jam
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success">
                                        Rp <?= number_format($overtime_pay, 0, ',', '.') ?>
                                    </div>
                                    <div class="small text-muted">@ Rp <?= number_format($hourly_rate, 0, ',', '.') ?>/jam</div>
                                </td>
                                <td>
                                    <?php
                                    $status = $overtime['status'] ?? 'pending';
                                    $statusColor = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                                    $statusIcon = $status === 'approved' ? 'check-circle' : ($status === 'rejected' ? 'x-circle' : 'clock');
                                    ?>
                                    <div class="badge bg-<?= $statusColor ?>-soft text-<?= $statusColor ?>">
                                        <i data-feather="<?= $statusIcon ?>" class="feather-xs me-1"></i>
                                        <?= ucfirst($status) ?>
                                    </div>
                                </td>
                                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/overtime/edit/<?= $overtime['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                                <?php if (($overtime['status'] ?? 'pending') === 'pending'): ?>
                                                    <a class="dropdown-item text-success" href="/overtime/approve/<?= $overtime['id'] ?>">
                                                        <div class="dropdown-item-icon">
                                                            <i data-feather="check"></i>
                                                        </div>
                                                        Setujui
                                                    </a>
                                                    <a class="dropdown-item text-danger" href="/overtime/reject/<?= $overtime['id'] ?>">
                                                        <div class="dropdown-item-icon">
                                                            <i data-feather="x"></i>
                                                        </div>
                                                        Tolak
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="3" class="fw-bold">Total Lembur:</td>
                            <td class="fw-bold">
                                <div class="badge bg-warning-soft text-warning">
                                    <?= number_format($total_hours, 1) ?> jam
                                </div>
                            </td>
                            <td class="fw-bold text-success fs-6">
                                Rp <?= number_format($total_pay, 0, ',', '.') ?>
                            </td>
                            <td colspan="<?= $_SESSION['user']['role'] !== 'staff' ? 2 : 1 ?>"></td>
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