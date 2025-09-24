<?php
$title = ($editing ? 'Edit' : 'Tambah') . ' Penjualan';
$currentPage = 'sales';
ob_start();
?>

<!-- Page Header -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="me-2">
                <i data-feather="<?= $editing ? 'edit-3' : 'plus-circle' ?>" class="text-success"></i>
            </div>
            <h4 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Data Penjualan</h4>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i data-feather="shopping-cart" class="me-2"></i>
            Form Data Penjualan
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= $editing ? '/sales/edit/' . $sale['id'] : '/sales/new' ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small mb-1" for="store_id">
                        <i data-feather="home" class="feather-xs me-1"></i>
                        Toko
                    </label>
                    <select class="form-select" id="store_id" name="store_id" required>
                        <option value="">Pilih Toko</option>
                        <?php foreach ($stores as $store): ?>
                            <option value="<?= $store['id'] ?>" <?= ($sale['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
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
                    <input class="form-control" id="date" name="date" type="date" value="<?= $sale['date'] ?? date('Y-m-d') ?>" required />
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label small mb-1" for="total_sales">
                        <i data-feather="dollar-sign" class="feather-xs me-1"></i>
                        Total Penjualan (Rupiah)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input class="form-control" id="total_sales" name="total_sales" type="number" value="<?= $sale['total_sales'] ?? '' ?>" min="0" step="1000" required placeholder="0" />
                    </div>
                    <div class="form-text">
                        <i data-feather="info" class="feather-xs me-1"></i>
                        Masukkan total penjualan dalam rupiah (kelipatan 1000)
                    </div>
                </div>
            </div>
            
            <!-- Preview Card -->
            <div class="card bg-light mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i data-feather="eye" class="me-2"></i>
                        Preview Data
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i data-feather="home" class="text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Toko</div>
                                    <div id="preview-store">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i data-feather="calendar" class="text-info me-2"></i>
                                <div>
                                    <div class="small text-muted">Tanggal</div>
                                    <div id="preview-date">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i data-feather="dollar-sign" class="text-success me-2"></i>
                                <div>
                                    <div class="small text-muted">Total</div>
                                    <div id="preview-amount" class="fw-bold text-success">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-between">
                <a href="/sales" class="btn btn-light">
                    <i data-feather="arrow-left" class="feather-xs me-1"></i>
                    Kembali
                </a>
                <div>
                    <button type="reset" class="btn btn-outline-secondary me-2">
                        <i data-feather="refresh-cw" class="feather-xs me-1"></i>
                        Reset
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i data-feather="<?= $editing ? 'save' : 'plus' ?>" class="feather-xs me-1"></i>
                        <?= $editing ? 'Update' : 'Simpan' ?> Penjualan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Live preview updates
document.addEventListener('DOMContentLoaded', function() {
    const storeSelect = document.getElementById('store_id');
    const dateInput = document.getElementById('date');
    const amountInput = document.getElementById('total_sales');
    
    function updatePreview() {
        // Store name
        const selectedStore = storeSelect.options[storeSelect.selectedIndex];
        document.getElementById('preview-store').textContent = selectedStore.value ? selectedStore.text : '-';
        
        // Date
        document.getElementById('preview-date').textContent = dateInput.value ? new Date(dateInput.value).toLocaleDateString('id-ID') : '-';
        
        // Amount
        const amount = amountInput.value ? parseInt(amountInput.value) : 0;
        document.getElementById('preview-amount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
    }
    
    storeSelect.addEventListener('change', updatePreview);
    dateInput.addEventListener('change', updatePreview);
    amountInput.addEventListener('input', updatePreview);
    
    // Initial update
    updatePreview();
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>