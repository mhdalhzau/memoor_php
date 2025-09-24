<?php
$title = 'Proposals Management';
$currentPage = 'proposals';
ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i data-feather="message-circle" class="me-1"></i>
            Filter Saran & Masukan
        </div>
        <a href="/proposals/new" class="btn btn-primary btn-sm">
            <i data-feather="plus" class="me-1"></i>
            Buat Saran Baru
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="small mb-1" for="category">Kategori</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Semua Kategori</option>
                    <option value="operational" <?= ($_GET['category'] ?? '') === 'operational' ? 'selected' : '' ?>>Operasional</option>
                    <option value="service" <?= ($_GET['category'] ?? '') === 'service' ? 'selected' : '' ?>>Pelayanan</option>
                    <option value="facilities" <?= ($_GET['category'] ?? '') === 'facilities' ? 'selected' : '' ?>>Fasilitas</option>
                    <option value="inventory" <?= ($_GET['category'] ?? '') === 'inventory' ? 'selected' : '' ?>>Inventory</option>
                    <option value="safety" <?= ($_GET['category'] ?? '') === 'safety' ? 'selected' : '' ?>>Keselamatan</option>
                </select>
            </div>
            
            <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
            <div class="col-md-4">
                <label class="small mb-1" for="store_id">Toko</label>
                <select class="form-select" id="store_id" name="store_id">
                    <option value="">Semua Toko</option>
                    <?php foreach ($stores as $store): ?>
                        <option value="<?= $store['id'] ?>" <?= ($_GET['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($store['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-outline-primary" type="submit">
                    <i data-feather="search" class="me-1"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Proposals List -->
<?php if (empty($proposals_list)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="text-muted">
                <i data-feather="message-square" class="feather-xl mb-3"></i>
                <h5>Belum ada saran yang dibuat</h5>
                <p>Mulai berkontribusi dengan membuat saran untuk perbaikan sistem.</p>
                <a href="/proposals/new" class="btn btn-primary">
                    <i data-feather="plus" class="me-1"></i>
                    Buat Saran Pertama
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($proposals_list as $proposal): ?>
            <div class="col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">
                                <i data-feather="lightbulb" class="me-2"></i>
                                <?= htmlspecialchars($proposal['title']) ?>
                            </h5>
                            <div class="card-subtitle text-muted small">
                                <div class="d-flex align-items-center gap-3">
                                    <span>
                                        <i data-feather="user" class="feather-xs me-1"></i>
                                        <?= htmlspecialchars($proposal['employee_name']) ?>
                                    </span>
                                    <span>
                                        <i data-feather="home" class="feather-xs me-1"></i>
                                        <?= htmlspecialchars($proposal['store_name']) ?>
                                    </span>
                                    <span>
                                        <i data-feather="calendar" class="feather-xs me-1"></i>
                                        <?= date('d/m/Y', strtotime($proposal['created_at'] ?? 'today')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <?php
                            $categoryColors = [
                                'operational' => 'primary',
                                'service' => 'success', 
                                'facilities' => 'info',
                                'inventory' => 'warning',
                                'safety' => 'danger'
                            ];
                            $categoryColor = $categoryColors[$proposal['category']] ?? 'secondary';
                            ?>
                            <div class="badge bg-<?= $categoryColor ?>-soft text-<?= $categoryColor ?>">
                                <?= ucfirst($proposal['category']) ?>
                            </div>
                            
                            <?php
                            $status = $proposal['status'] ?? 'pending';
                            $statusColors = [
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'pending' => 'warning'
                            ];
                            $statusColor = $statusColors[$status] ?? 'secondary';
                            ?>
                            <div class="badge bg-<?= $statusColor ?>-soft text-<?= $statusColor ?>">
                                <i data-feather="<?= $status === 'approved' ? 'check-circle' : ($status === 'rejected' ? 'x-circle' : 'clock') ?>" class="feather-xs me-1"></i>
                                <?= ucfirst($status) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <p class="card-text"><?= nl2br(htmlspecialchars($proposal['description'] ?? 'Tidak ada deskripsi')) ?></p>
                        
                        <?php if ($proposal['estimated_cost']): ?>
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center">
                                    <i data-feather="dollar-sign" class="me-2"></i>
                                    <strong>Estimasi Biaya:</strong>
                                    <span class="ms-2 fw-bold">Rp <?= number_format($proposal['estimated_cost'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($proposal['response'] ?? ''): ?>
                            <div class="alert alert-info">
                                <div class="d-flex align-items-start">
                                    <i data-feather="message-square" class="me-2 mt-1"></i>
                                    <div>
                                        <strong>Tanggapan Management:</strong>
                                        <p class="mb-0 mt-1"><?= htmlspecialchars($proposal['response']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i data-feather="clock" class="feather-xs me-1"></i>
                                Dibuat <?= date('d M Y, H:i', strtotime($proposal['created_at'] ?? 'today')) ?>
                            </div>
                            
                            <div class="d-flex gap-1">
                                <?php if ($proposal['user_id'] === $_SESSION['user']['id'] || $_SESSION['user']['role'] !== 'staff'): ?>
                                    <a href="/proposals/edit/<?= $proposal['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i data-feather="edit-3" class="feather-xs"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($_SESSION['user']['role'] !== 'staff' && ($proposal['status'] ?? 'pending') === 'pending'): ?>
                                    <a href="/proposals/approve/<?= $proposal['id'] ?>" class="btn btn-success btn-sm">
                                        <i data-feather="check" class="feather-xs me-1"></i>
                                        Setujui
                                    </a>
                                    <a href="/proposals/reject/<?= $proposal['id'] ?>" class="btn btn-danger btn-sm">
                                        <i data-feather="x" class="feather-xs me-1"></i>
                                        Tolak
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>