<?php
$title = 'Gaji - SPBU Management System';
$currentPage = 'payroll';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Data Gaji Karyawan</h2>
    <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
        <a href="/payroll/new" class="btn">Proses Gaji Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="month">Bulan:</label>
            <input type="month" id="month" name="month" value="<?= $_GET['month'] ?? date('Y-m') ?>">
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
    
    <?php if (empty($payroll_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data gaji untuk bulan yang dipilih.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Toko</th>
                    <th>Bulan</th>
                    <th>Gaji Pokok</th>
                    <th>Total Absensi</th>
                    <th>Total Lembur</th>
                    <th>Total Gaji</th>
                    <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_payroll = 0;
                foreach ($payroll_list as $payroll): 
                    $overtime_pay = ($payroll['overtime_hours'] ?? 0) * 15000; // Rp 15k per jam
                    $total_pay = $payroll['base_salary'] + $overtime_pay;
                    $total_payroll += $total_pay;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($payroll['employee_name']) ?></td>
                        <td><?= htmlspecialchars($payroll['store_name']) ?></td>
                        <td><?= date('M Y', strtotime($payroll['month'] . '-01')) ?></td>
                        <td>Rp <?= number_format($payroll['base_salary'], 0, ',', '.') ?></td>
                        <td><?= $payroll['attendance_count'] ?? 0 ?> hari</td>
                        <td><?= $payroll['overtime_hours'] ?? 0 ?> jam</td>
                        <td>Rp <?= number_format($total_pay, 0, ',', '.') ?></td>
                        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                            <td>
                                <a href="/payroll/edit/<?= $payroll['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <a href="/payroll/slip/<?= $payroll['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background: #28a745;">Slip</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f8f9fa;">
                    <td colspan="6">Total Penggajian:</td>
                    <td>Rp <?= number_format($total_payroll, 0, ',', '.') ?></td>
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