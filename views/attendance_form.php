<?php
$title = ($editing ? 'Edit' : 'Tambah') . ' Absensi';
$currentPage = 'attendance';
ob_start();
?>

<!-- Page Header -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="me-2">
                <i data-feather="<?= $editing ? 'edit-3' : 'plus-circle' ?>" class="text-primary"></i>
            </div>
            <h4 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Data Absensi</h4>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i data-feather="user-check" class="me-2"></i>
            Form Absensi Karyawan
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= $editing ? '/attendance/edit/' . $attendance['id'] : '/attendance/new' ?>">
            <div class="row">
                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="user_id">
                        <i data-feather="user" class="feather-xs me-1"></i>
                        Karyawan
                    </label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Pilih Karyawan</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= ($attendance['user_id'] ?? $_SESSION['user']['id']) == $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="store_id">
                        <i data-feather="home" class="feather-xs me-1"></i>
                        Toko
                    </label>
                    <select class="form-select" id="store_id" name="store_id" required>
                        <option value="">Pilih Toko</option>
                        <?php foreach ($stores as $store): ?>
                            <option value="<?= $store['id'] ?>" <?= ($attendance['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($store['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="date">
                        <i data-feather="calendar" class="feather-xs me-1"></i>
                        Tanggal
                    </label>
                    <input class="form-control" id="date" name="date" type="date" value="<?= $attendance['date'] ?? date('Y-m-d') ?>" required />
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="shift">
                        <i data-feather="clock" class="feather-xs me-1"></i>
                        Shift
                    </label>
                    <select class="form-select" id="shift" name="shift">
                        <option value="">Pilih Shift</option>
                        <option value="pagi" <?= ($attendance['shift'] ?? '') === 'pagi' ? 'selected' : '' ?>>Pagi (07:00-15:00)</option>
                        <option value="sore" <?= ($attendance['shift'] ?? '') === 'sore' ? 'selected' : '' ?>>Sore (15:00-23:00)</option>
                        <option value="malam" <?= ($attendance['shift'] ?? '') === 'malam' ? 'selected' : '' ?>>Malam (23:00-07:00)</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="check_in">
                        <i data-feather="log-in" class="feather-xs me-1"></i>
                        Check In
                    </label>
                    <input class="form-control" id="check_in" name="check_in" type="time" value="<?= $attendance['check_in'] ?? '' ?>" />
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="check_out">
                        <i data-feather="log-out" class="feather-xs me-1"></i>
                        Check Out
                    </label>
                    <input class="form-control" id="check_out" name="check_out" type="time" value="<?= $attendance['check_out'] ?? '' ?>" />
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label small mb-1" for="lateness_minutes">
                        <i data-feather="alert-triangle" class="feather-xs me-1"></i>
                        Keterlambatan (menit)
                    </label>
                    <input class="form-control" id="lateness_minutes" name="lateness_minutes" type="number" value="<?= $attendance['lateness_minutes'] ?? 0 ?>" min="0" />
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label small mb-1" for="overtime_minutes">
                        <i data-feather="trending-up" class="feather-xs me-1"></i>
                        Lembur (menit)
                    </label>
                    <input class="form-control" id="overtime_minutes" name="overtime_minutes" type="number" value="<?= $attendance['overtime_minutes'] ?? 0 ?>" min="0" />
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label small mb-1" for="break_duration">
                        <i data-feather="pause-circle" class="feather-xs me-1"></i>
                        Durasi Istirahat (menit)
                    </label>
                    <input class="form-control" id="break_duration" name="break_duration" type="number" value="<?= $attendance['break_duration'] ?? 60 ?>" min="0" />
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-between">
                <a href="/attendance" class="btn btn-light">
                    <i data-feather="arrow-left" class="feather-xs me-1"></i>
                    Kembali
                </a>
                <div>
                    <button type="reset" class="btn btn-outline-secondary me-2">
                        <i data-feather="refresh-cw" class="feather-xs me-1"></i>
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="<?= $editing ? 'save' : 'plus' ?>" class="feather-xs me-1"></i>
                        <?= $editing ? 'Update' : 'Simpan' ?> Absensi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>