<?php
$title = 'Attendance Management';
$currentPage = 'attendance';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="clock" class="me-1"></i>
            Filter Data Absensi
        </div>
        <a href="/attendance/new" class="btn btn-primary btn-sm">
            <i data-feather="plus" class="me-1"></i>
            Catat Absensi Baru
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
        <i data-feather="users" class="me-1"></i>
        Data Absensi
    </div>
    <div class="card-body">
        <?php if (empty($attendance_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="calendar-x" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data absensi untuk tanggal yang dipilih.</p>
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
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_list as $attendance): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img class="avatar-img img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($attendance['employee_name']) ?>&background=random" />
                                        </div>
                                        <div class="fw-500"><?= htmlspecialchars($attendance['employee_name']) ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($attendance['store_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($attendance['date'])) ?></td>
                                <td>
                                    <?php if ($attendance['check_in']): ?>
                                        <div class="badge bg-success-soft text-success"><?= $attendance['check_in'] ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($attendance['check_out']): ?>
                                        <div class="badge bg-primary-soft text-primary"><?= $attendance['check_out'] ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $attendance['shift'] ?: '-' ?></td>
                                <td>
                                    <div class="small">
                                        <?php if ($attendance['lateness_minutes']): ?>
                                            <div class="badge bg-danger-soft text-danger mb-1">
                                                Terlambat <?= $attendance['lateness_minutes'] ?> menit
                                            </div><br>
                                        <?php endif; ?>
                                        <?php if ($attendance['overtime_minutes']): ?>
                                            <div class="badge bg-warning-soft text-warning">
                                                Lembur <?= $attendance['overtime_minutes'] ?> menit
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!$attendance['lateness_minutes'] && !$attendance['overtime_minutes']): ?>
                                            <div class="badge bg-success-soft text-success">Normal</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/attendance/edit/<?= $attendance['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
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