<?php
$title = 'Toko - SPBU Management System';
$currentPage = 'stores';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Data Toko</h2>
    <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
        <a href="/stores/new" class="btn">Tambah Toko Baru</a>
    <?php endif; ?>
</div>

<div class="card">
    <?php if (empty($stores_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Tidak ada data toko.
        </p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Toko</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Manager</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stores_list as $store): ?>
                    <tr>
                        <td><?= $store['id'] ?></td>
                        <td><?= htmlspecialchars($store['name']) ?></td>
                        <td><?= htmlspecialchars($store['address'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($store['phone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($store['manager'] ?? '-') ?></td>
                        <td>
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                <?= $store['status'] === 'active' ? '#27ae60' : '#e74c3c' ?>; 
                                color: white;">
                                <?= ucfirst($store['status']) ?>
                            </span>
                        </td>
                        <td><?= $store['entry_time_start'] ?> - <?= $store['entry_time_end'] ?></td>
                        <td><?= $store['exit_time_start'] ?> - <?= $store['exit_time_end'] ?></td>
                        <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                            <td>
                                <a href="/stores/edit/<?= $store['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <?php if ($_SESSION['user']['role'] === 'administrasi'): ?>
                                    <a href="/stores/delete/<?= $store['id'] ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Yakin hapus toko ini?')">Hapus</a>
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