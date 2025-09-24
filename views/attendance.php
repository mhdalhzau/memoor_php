<?php
$title = 'Absensi - SPBU Management System';
$currentPage = 'attendance';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Data Absensi</h2>
    <a href="/attendance/new" class="btn">Catat Absensi Baru</a>
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
    
    <?php if (empty($attendance_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data absensi untuk tanggal yang dipilih.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Toko</th>
                    <th>Tanggal</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Shift</th>
                    <th>Terlambat</th>
                    <th>Lembur</th>
                    <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_list as $attendance): ?>
                    <tr>
                        <td><?= htmlspecialchars($attendance['employee_name']) ?></td>
                        <td><?= htmlspecialchars($attendance['store_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($attendance['date'])) ?></td>
                        <td><?= $attendance['check_in'] ?: '-' ?></td>
                        <td><?= $attendance['check_out'] ?: '-' ?></td>
                        <td><?= $attendance['shift'] ?: '-' ?></td>
                        <td><?= $attendance['lateness_minutes'] ? $attendance['lateness_minutes'] . ' menit' : '-' ?></td>
                        <td><?= $attendance['overtime_minutes'] ? $attendance['overtime_minutes'] . ' menit' : '-' ?></td>
                        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                            <td>
                                <a href="/attendance/edit/<?= $attendance['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
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