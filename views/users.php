<?php
$title = 'Users Management';
$currentPage = 'users';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="users" class="me-1"></i>
            Filter Data Karyawan
        </div>
        <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
            <a href="/users/new" class="btn btn-primary btn-sm">
                <i data-feather="user-plus" class="me-1"></i>
                Tambah Karyawan Baru
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="small mb-1" for="role">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Semua Role</option>
                    <option value="staff" <?= ($_GET['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="manager" <?= ($_GET['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                    <option value="administrasi" <?= ($_GET['role'] ?? '') === 'administrasi' ? 'selected' : '' ?>>Administrasi</option>
                </select>
            </div>
            
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
        Data Karyawan
    </div>
    <div class="card-body">
        <?php if (empty($users_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="user-x" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data karyawan.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Kontak</th>
                            <th>Gaji</th>
                            <th>Toko Assigned</th>
                            <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users_list as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img class="avatar-img img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random" />
                                        </div>
                                        <div class="fw-500"><?= htmlspecialchars($user['name']) ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = 'primary';
                                    if ($user['role'] === 'manager') $badgeClass = 'primary';
                                    elseif ($user['role'] === 'administrasi') $badgeClass = 'warning';
                                    else $badgeClass = 'secondary';
                                    ?>
                                    <div class="badge bg-<?= $badgeClass ?>-soft text-<?= $badgeClass ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($user['phone']): ?>
                                        <div class="small">
                                            <i data-feather="phone" class="feather-xs me-1"></i>
                                            <?= htmlspecialchars($user['phone']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['salary']): ?>
                                        <div class="fw-bold text-success">
                                            Rp <?= number_format($user['salary'], 0, ',', '.') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['stores']): ?>
                                        <div class="badge bg-info-soft text-info">
                                            <?= htmlspecialchars($user['stores']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/users/edit/<?= $user['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                                <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                                    <a class="dropdown-item text-danger" href="/users/delete/<?= $user['id'] ?>" onclick="return confirm('Yakin hapus karyawan ini?')">
                                                        <div class="dropdown-item-icon">
                                                            <i data-feather="trash-2"></i>
                                                        </div>
                                                        Hapus
                                                    </a>
                                                <?php endif; ?>
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