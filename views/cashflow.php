<?php
$title = 'Cashflow Management';
$currentPage = 'cashflow';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="trending-up" class="me-1"></i>
            Filter Arus Kas
        </div>
        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
            <a href="/cashflow/new" class="btn btn-primary btn-sm">
                <i data-feather="plus" class="me-1"></i>
                Tambah Transaksi
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="small mb-1" for="type">Tipe</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Semua Tipe</option>
                    <option value="income" <?= ($_GET['type'] ?? '') === 'income' ? 'selected' : '' ?>>Pemasukan</option>
                    <option value="expense" <?= ($_GET['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="small mb-1" for="category">Kategori</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Semua Kategori</option>
                    <option value="operational" <?= ($_GET['category'] ?? '') === 'operational' ? 'selected' : '' ?>>Operasional</option>
                    <option value="sales" <?= ($_GET['category'] ?? '') === 'sales' ? 'selected' : '' ?>>Penjualan</option>
                    <option value="maintenance" <?= ($_GET['category'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    <option value="inventory" <?= ($_GET['category'] ?? '') === 'inventory' ? 'selected' : '' ?>>Inventory</option>
                </select>
            </div>
            
            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
            <div class="col-md-3">
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

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-4 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small fw-bold text-success mb-1">Total Pemasukan</div>
                        <div class="h5">Rp <?= number_format($summary['total_income'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="flex-shrink-0"><i data-feather="trending-up" class="text-success"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card border-left-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small fw-bold text-danger mb-1">Total Pengeluaran</div>
                        <div class="h5">Rp <?= number_format($summary['total_expense'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="flex-shrink-0"><i data-feather="trending-down" class="text-danger"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small fw-bold text-primary mb-1">Saldo Bersih</div>
                        <div class="h5">Rp <?= number_format(($summary['total_income'] ?? 0) - ($summary['total_expense'] ?? 0), 0, ',', '.') ?></div>
                    </div>
                    <div class="flex-shrink-0"><i data-feather="dollar-sign" class="text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Card -->
<div class="card">
    <div class="card-header">
        <i data-feather="activity" class="me-1"></i>
        Data Arus Kas
    </div>
    <div class="card-body">
        <?php if (empty($cashflow_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="dollar-sign" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data transaksi kas.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Toko</th>
                            <th>Kategori</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cashflow_list as $cashflow): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($cashflow['created_at'] ?? 'today')) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-img rounded-circle bg-primary-soft text-primary d-flex align-items-center justify-content-center">
                                                <i data-feather="home"></i>
                                            </div>
                                        </div>
                                        <?= htmlspecialchars($cashflow['store_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge bg-info-soft text-info">
                                        <?= ucfirst($cashflow['category']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge bg-<?= $cashflow['type'] === 'income' ? 'success' : 'danger' ?>-soft text-<?= $cashflow['type'] === 'income' ? 'success' : 'danger' ?>">
                                        <i data-feather="<?= $cashflow['type'] === 'income' ? 'arrow-up' : 'arrow-down' ?>" class="feather-xs me-1"></i>
                                        <?= $cashflow['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-<?= $cashflow['type'] === 'income' ? 'success' : 'danger' ?>">
                                        <?= $cashflow['type'] === 'income' ? '+' : '-' ?>Rp <?= number_format($cashflow['amount'], 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small"><?= htmlspecialchars($cashflow['description'] ?? '-') ?></div>
                                </td>
                                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/cashflow/edit/<?= $cashflow['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                                <a class="dropdown-item text-danger" href="/cashflow/delete/<?= $cashflow['id'] ?>" onclick="return confirm('Yakin hapus transaksi ini?')">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="trash-2"></i>
                                                    </div>
                                                    Hapus
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