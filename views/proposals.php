<?php
$title = 'Saran - SPBU Management System';
$currentPage = 'proposals';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Saran & Masukan</h2>
    <a href="/proposals/new" class="btn">Buat Saran Baru</a>
</div>

<div class="card">
    <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="category">Kategori:</label>
            <select id="category" name="category">
                <option value="">Semua Kategori</option>
                <option value="operational" <?= ($_GET['category'] ?? '') === 'operational' ? 'selected' : '' ?>>Operasional</option>
                <option value="service" <?= ($_GET['category'] ?? '') === 'service' ? 'selected' : '' ?>>Pelayanan</option>
                <option value="facilities" <?= ($_GET['category'] ?? '') === 'facilities' ? 'selected' : '' ?>>Fasilitas</option>
                <option value="inventory" <?= ($_GET['category'] ?? '') === 'inventory' ? 'selected' : '' ?>>Inventory</option>
                <option value="safety" <?= ($_GET['category'] ?? '') === 'safety' ? 'selected' : '' ?>>Keselamatan</option>
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
    
    <?php if (empty($proposals_list)): ?>
        <p style="text-align: center; color: #666; padding: 2rem;">
            Belum ada saran yang dibuat.
        </p>
    <?php else: ?>
        <div style="display: grid; gap: 1rem;">
            <?php foreach ($proposals_list as $proposal): ?>
                <div style="border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; background: white;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem; color: #667eea;"><?= htmlspecialchars($proposal['title']) ?></h4>
                            <div style="display: flex; gap: 1rem; font-size: 0.9rem; color: #666;">
                                <span>oleh <strong><?= htmlspecialchars($proposal['employee_name']) ?></strong></span>
                                <span>•</span>
                                <span><?= htmlspecialchars($proposal['store_name']) ?></span>
                                <span>•</span>
                                <span><?= date('d/m/Y', strtotime($proposal['created_at'] ?? 'today')) ?></span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: #667eea; color: white;">
                                <?= ucfirst($proposal['category']) ?>
                            </span>
                            <?php if ($proposal['status'] ?? 'pending'): ?>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; background: 
                                    <?= ($proposal['status'] ?? 'pending') === 'approved' ? '#27ae60' : 
                                        (($proposal['status'] ?? 'pending') === 'rejected' ? '#e74c3c' : '#f39c12') ?>; 
                                    color: white;">
                                    <?= ucfirst($proposal['status'] ?? 'pending') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <p style="margin: 0 0 1rem; line-height: 1.5;">
                        <?= nl2br(htmlspecialchars($proposal['description'] ?? 'Tidak ada deskripsi')) ?>
                    </p>
                    
                    <?php if ($proposal['estimated_cost']): ?>
                        <p style="margin: 0 0 1rem; font-weight: bold; color: #e74c3c;">
                            Estimasi Biaya: Rp <?= number_format($proposal['estimated_cost'], 0, ',', '.') ?>
                        </p>
                    <?php endif; ?>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.9rem; color: #666;">
                            <?php if ($proposal['response'] ?? ''): ?>
                                <strong>Tanggapan:</strong> <?= htmlspecialchars($proposal['response']) ?>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <?php if ($proposal['user_id'] === $_SESSION['user']['id'] || $_SESSION['user']['role'] !== 'staff'): ?>
                                <a href="/proposals/edit/<?= $proposal['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                            <?php endif; ?>
                            
                            <?php if ($_SESSION['user']['role'] !== 'staff' && ($proposal['status'] ?? 'pending') === 'pending'): ?>
                                <a href="/proposals/approve/<?= $proposal['id'] ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background: #27ae60;">Setujui</a>
                                <a href="/proposals/reject/<?= $proposal['id'] ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Tolak</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>