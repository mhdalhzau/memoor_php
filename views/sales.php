<?php
$title = 'Sales Management';
$currentPage = 'sales';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="shopping-cart" class="me-1"></i>
            Filter Data Penjualan
        </div>
        <a href="/sales/new" class="btn btn-primary btn-sm">
            <i data-feather="plus" class="me-1"></i>
            Input Penjualan Baru
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="small mb-1" for="start_date">Dari Tanggal</label>
                <input class="form-control" id="start_date" name="start_date" type="date" value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>" />
            </div>
            
            <div class="col-md-3">
                <label class="small mb-1" for="end_date">Sampai Tanggal</label>
                <input class="form-control" id="end_date" name="end_date" type="date" value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>" />
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
            
            <div class="col-md-3 d-flex align-items-end">
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
        Data Penjualan
    </div>
    <div class="card-body">
        <?php if (empty($sales_list)): ?>
            <div class="text-center py-4">
                <div class="text-muted">
                    <i data-feather="shopping-cart" class="feather-xl mb-2"></i>
                    <p class="mt-3">Tidak ada data penjualan untuk periode yang dipilih.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped" id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Toko</th>
                            <th>Karyawan</th>
                            <th>Total Penjualan</th>
                            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_sales = 0;
                        foreach ($sales_list as $sale): 
                            $total_sales += $sale['total_sales'];
                        ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-img rounded-circle bg-success-soft text-success d-flex align-items-center justify-content-center">
                                                <i data-feather="home"></i>
                                            </div>
                                        </div>
                                        <div class="fw-500"><?= htmlspecialchars($sale['store_name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <img class="avatar-img img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($sale['user_name']) ?>&background=random" />
                                        </div>
                                        <?= htmlspecialchars($sale['user_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success">
                                        Rp <?= number_format($sale['total_sales'], 0, ',', '.') ?>
                                    </div>
                                </td>
                                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-datatable btn-icon btn-transparent-dark" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/sales/edit/<?= $sale['id'] ?>">
                                                    <div class="dropdown-item-icon">
                                                        <i data-feather="edit-3"></i>
                                                    </div>
                                                    Edit
                                                </a>
                                                <a class="dropdown-item text-danger" href="/sales/delete/<?= $sale['id'] ?>" onclick="return confirm('Yakin hapus data penjualan ini?')">
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
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="3" class="fw-bold">Total Penjualan:</td>
                            <td class="fw-bold text-success fs-5">
                                Rp <?= number_format($total_sales, 0, ',', '.') ?>
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