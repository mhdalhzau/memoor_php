<?php
$title = 'Penjualan - SPBU Management System';
$currentPage = 'sales';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Data Penjualan</h2>
    <a href="/sales/new" class="btn">Input Penjualan Baru</a>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="start_date">Dari Tanggal:</label>
            <input type="date" id="start_date" name="start_date" value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>">
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label for="end_date">Sampai Tanggal:</label>
            <input type="date" id="end_date" name="end_date" value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>">
        </div>
        
        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
        <div class="form-group" style="margin-bottom: 0;">
            <label for="store_id">Toko:</label>
            <select id="store_id" name="store_id">
                <option value="">Semua Toko</option>
                <?php foreach ($stores as $store): ?>
                    <option value="<?= $store['id'] ?>" <?= ($_GET['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($store['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <button type="submit" class="btn">Filter</button>
    </form>
    
    <?php if (empty($sales_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data penjualan untuk periode yang dipilih.
        </p>
    <?php else: ?>
        <table class="table">
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
                        <td><?= htmlspecialchars($sale['store_name']) ?></td>
                        <td><?= htmlspecialchars($sale['user_name']) ?></td>
                        <td>Rp <?= number_format($sale['total_sales'], 0, ',', '.') ?></td>
                        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                            <td>
                                <a href="/sales/edit/<?= $sale['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <a href="/sales/delete/<?= $sale['id'] ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Yakin hapus data penjualan ini?')">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f8f9fa;">
                    <td colspan="3">Total Penjualan:</td>
                    <td>Rp <?= number_format($total_sales, 0, ',', '.') ?></td>
                    <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                        <td></td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>