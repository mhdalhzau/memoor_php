<?php
$title = 'Kas - SPBU Management System';
$currentPage = 'cashflow';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Data Arus Kas</h2>
    <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
        <a href="/cashflow/new" class="btn">Tambah Transaksi</a>
    <?php endif; ?>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="type">Tipe:</label>
            <select id="type" name="type">
                <option value="">Semua Tipe</option>
                <option value="income" <?= ($_GET['type'] ?? '') === 'income' ? 'selected' : '' ?>>Pemasukan</option>
                <option value="expense" <?= ($_GET['type'] ?? '') === 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label for="category">Kategori:</label>
            <select id="category" name="category">
                <option value="">Semua Kategori</option>
                <option value="operational" <?= ($_GET['category'] ?? '') === 'operational' ? 'selected' : '' ?>>Operasional</option>
                <option value="sales" <?= ($_GET['category'] ?? '') === 'sales' ? 'selected' : '' ?>>Penjualan</option>
                <option value="maintenance" <?= ($_GET['category'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                <option value="inventory" <?= ($_GET['category'] ?? '') === 'inventory' ? 'selected' : '' ?>>Inventory</option>
            </select>
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
    
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: #d4edda; padding: 1rem; border-radius: 8px; text-align: center;">
            <h3 style="color: #155724; margin: 0;">Rp <?= number_format($summary['total_income'] ?? 0, 0, ',', '.') ?></h3>
            <p style="color: #155724; margin: 0.5rem 0 0;">Total Pemasukan</p>
        </div>
        <div style="background: #f8d7da; padding: 1rem; border-radius: 8px; text-align: center;">
            <h3 style="color: #721c24; margin: 0;">Rp <?= number_format($summary['total_expense'] ?? 0, 0, ',', '.') ?></h3>
            <p style="color: #721c24; margin: 0.5rem 0 0;">Total Pengeluaran</p>
        </div>
        <div style="background: #cce5ff; padding: 1rem; border-radius: 8px; text-align: center;">
            <h3 style="color: #004085; margin: 0;">Rp <?= number_format(($summary['total_income'] ?? 0) - ($summary['total_expense'] ?? 0), 0, ',', '.') ?></h3>
            <p style="color: #004085; margin: 0.5rem 0 0;">Saldo Bersih</p>
        </div>
    </div>
    
    <?php if (empty($cashflow_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data transaksi kas.
        </p>
    <?php else: ?>
        <table class="table">
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
                        <td><?= htmlspecialchars($cashflow['store_name']) ?></td>
                        <td><?= ucfirst($cashflow['category']) ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= $cashflow['type'] === 'income' ? '#27ae60' : '#e74c3c' ?>; 
                                color: white;">
                                <?= $cashflow['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' ?>
                            </span>
                        </td>
                        <td style="color: <?= $cashflow['type'] === 'income' ? '#27ae60' : '#e74c3c' ?>; font-weight: bold;">
                            <?= $cashflow['type'] === 'income' ? '+' : '-' ?>Rp <?= number_format($cashflow['amount'], 0, ',', '.') ?>
                        </td>
                        <td><?= htmlspecialchars($cashflow['description'] ?? '-') ?></td>
                        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                            <td>
                                <a href="/cashflow/edit/<?= $cashflow['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <a href="/cashflow/delete/<?= $cashflow['id'] ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>