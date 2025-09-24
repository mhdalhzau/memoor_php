<?php
$title = 'Lembur - SPBU Management System';
$currentPage = 'overtime';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manajemen Lembur</h2>
    <a href="/overtime/new" class="btn">Input Lembur Baru</a>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="date">Tanggal:</label>
            <input type="date" id="date" name="date" value="<?= $_GET['date'] ?? date('Y-m-d') ?>">
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
    
    <?php if (empty($overtime_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data lembur untuk tanggal yang dipilih.
        </p>
    <?php else: ?>
        <table class="table">
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
                        <td><?= htmlspecialchars($overtime['employee_name']) ?></td>
                        <td><?= htmlspecialchars($overtime['store_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($overtime['date'])) ?></td>
                        <td><?= number_format($overtime['hours'], 1) ?> jam</td>
                        <td>Rp <?= number_format($overtime_pay, 0, ',', '.') ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= ($overtime['status'] ?? 'pending') === 'approved' ? '#27ae60' : '#f39c12' ?>; 
                                color: white;">
                                <?= ucfirst($overtime['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                            <td>
                                <a href="/overtime/edit/<?= $overtime['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <?php if (($overtime['status'] ?? 'pending') === 'pending'): ?>
                                    <a href="/overtime/approve/<?= $overtime['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background: #27ae60;">Setujui</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f8f9fa;">
                    <td colspan="3">Total:</td>
                    <td><?= number_format($total_hours, 1) ?> jam</td>
                    <td>Rp <?= number_format($total_pay, 0, ',', '.') ?></td>
                    <td colspan="<?= $_SESSION['user']['role'] !== 'staff' ? 2 : 1 ?>"></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>