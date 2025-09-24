<?php
$title = 'Karyawan - SPBU Management System';
$currentPage = 'users';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Data Karyawan</h2>
    <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
        <a href="/users/new" class="btn">Tambah Karyawan Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="">Semua Role</option>
                <option value="staff" <?= ($_GET['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="manager" <?= ($_GET['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="administrasi" <?= ($_GET['role'] ?? '') === 'administrasi' ? 'selected' : '' ?>>Administrasi</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Filter</button>
    </form>
    
    <?php if (empty($users_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data karyawan.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Telepon</th>
                    <th>Gaji</th>
                    <th>Toko Assigned</th>
                    <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users_list as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= $user['role'] === 'manager' ? '#667eea' : ($user['role'] === 'administrasi' ? '#f39c12' : '#95a5a6') ?>; 
                                color: white;">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td><?= $user['phone'] ?: '-' ?></td>
                        <td><?= $user['salary'] ? 'Rp ' . number_format($user['salary'], 0, ',', '.') : '-' ?></td>
                        <td><?= $user['stores'] ?? '-' ?></td>
                        <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
                            <td>
                                <a href="/users/edit/<?= $user['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                    <a href="/users/delete/<?= $user['id'] ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Yakin hapus karyawan ini?')">Hapus</a>
                                <?php endif; ?>
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