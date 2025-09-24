<?php
$title = ($editing ? 'Edit' : 'Tambah') . ' Penjualan - SPBU Management System';
$currentPage = 'sales';
ob_start();
?>

<h2><?= $editing ? 'Edit' : 'Tambah' ?> Data Penjualan</h2>

<div class="card">
    <form method="POST" action="<?= $editing ? '/sales/edit/' . $sale['id'] : '/sales/new' ?>">
        <div class="form-group">
            <label for="store_id">Toko:</label>
            <select id="store_id" name="store_id" required>
                <option value="">Pilih Toko</option>
                <?php foreach ($stores as $store): ?>
                    <option value="<?= $store['id'] ?>" <?= ($sale['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($store['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Tanggal:</label>
            <input type="date" id="date" name="date" value="<?= $sale['date'] ?? date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="total_sales">Total Penjualan (Rp):</label>
            <input type="number" id="total_sales" name="total_sales" value="<?= $sale['total_sales'] ?? '' ?>" min="0" step="1000" required>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn"><?= $editing ? 'Update' : 'Simpan' ?> Penjualan</button>
            <a href="/sales" class="btn" style="background: #6c757d;">Batal</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>