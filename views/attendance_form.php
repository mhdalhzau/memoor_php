<?php
$title = ($editing ? 'Edit' : 'Tambah') . ' Absensi - SPBU Management System';
$currentPage = 'attendance';
ob_start();
?>

<h2><?= $editing ? 'Edit' : 'Tambah' ?> Absensi</h2>

<div class="card">
    <form method="POST" action="<?= $editing ? '/attendance/edit/' . $attendance['id'] : '/attendance/new' ?>">
        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
        <div class="form-group">
            <label for="user_id">Karyawan:</label>
            <select id="user_id" name="user_id" required>
                <option value="">Pilih Karyawan</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= ($attendance['user_id'] ?? $_SESSION['user']['id']) == $user['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="store_id">Toko:</label>
            <select id="store_id" name="store_id" required>
                <option value="">Pilih Toko</option>
                <?php foreach ($stores as $store): ?>
                    <option value="<?= $store['id'] ?>" <?= ($attendance['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($store['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Tanggal:</label>
            <input type="date" id="date" name="date" value="<?= $attendance['date'] ?? date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="check_in">Check In:</label>
            <input type="time" id="check_in" name="check_in" value="<?= $attendance['check_in'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="check_out">Check Out:</label>
            <input type="time" id="check_out" name="check_out" value="<?= $attendance['check_out'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="shift">Shift:</label>
            <select id="shift" name="shift">
                <option value="">Pilih Shift</option>
                <option value="pagi" <?= ($attendance['shift'] ?? '') === 'pagi' ? 'selected' : '' ?>>Pagi (07:00-15:00)</option>
                <option value="sore" <?= ($attendance['shift'] ?? '') === 'sore' ? 'selected' : '' ?>>Sore (15:00-23:00)</option>
                <option value="malam" <?= ($attendance['shift'] ?? '') === 'malam' ? 'selected' : '' ?>>Malam (23:00-07:00)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="lateness_minutes">Keterlambatan (menit):</label>
            <input type="number" id="lateness_minutes" name="lateness_minutes" value="<?= $attendance['lateness_minutes'] ?? 0 ?>" min="0">
        </div>
        
        <div class="form-group">
            <label for="overtime_minutes">Lembur (menit):</label>
            <input type="number" id="overtime_minutes" name="overtime_minutes" value="<?= $attendance['overtime_minutes'] ?? 0 ?>" min="0">
        </div>
        
        <div class="form-group">
            <label for="break_duration">Durasi Istirahat (menit):</label>
            <input type="number" id="break_duration" name="break_duration" value="<?= $attendance['break_duration'] ?? 60 ?>" min="0">
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn"><?= $editing ? 'Update' : 'Simpan' ?> Absensi</button>
            <a href="/attendance" class="btn" style="background: #6c757d;">Batal</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>