<?php
$title = 'Stores Management';
$currentPage = 'stores';
ob_start();
?>

<!-- Main page content-->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="home" class="me-1"></i>
            Data Toko
        </div>
        <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
            <a href="/stores/new" class="btn btn-primary btn-sm">
                <i data-feather="plus" class="me-1"></i>
                Tambah Toko Baru
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($stores_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="inbox" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data toko.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Toko</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Jam Operasional</th>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stores_list as $store): ?>
                            <tr>
                                <td><?= $store['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-img rounded-circle bg-primary-soft text-primary d-flex align-items-center justify-content-center">
                                                <i data-feather="home"></i>
                                            </div>
                                        </div>
                                        <div class="fw-500"><?= htmlspecialchars($store['name']) ?></div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($store['address'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($store['phone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($store['manager'] ?? '-') ?></td>
                                <td>
                                    <?php $status = $store['status'] ?? 'inactive'; ?>
                                    <div class="badge bg-<?= $status === 'active' ? 'success' : 'danger' ?>-soft text-<?= $status === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($status) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <strong>Masuk:</strong> <?= ($store['entry_time_start'] ?? '-') ?> - <?= ($store['entry_time_end'] ?? '-') ?><br>
                                        <strong>Keluar:</strong> <?= ($store['exit_time_start'] ?? '-') ?> - <?= ($store['exit_time_end'] ?? '-') ?>
                                    </div>
                                </td>
                                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/stores/edit/<?= $store['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                                <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
                                                    <a class="dropdown-item text-danger" href="/stores/delete/<?= $store['id'] ?>" onclick="return confirm('Yakin hapus toko ini?')">
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