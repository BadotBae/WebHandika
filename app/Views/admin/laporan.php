<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; --bg-body: #f8f9fa; }
        body { font-family: "Outfit", sans-serif; background-color: var(--bg-body); color: #2b2d42; }
        
        /* Sidebar Styles */
        .sidebar { background: white; min-height: 100vh; border-right: 1px solid #eee; position: fixed; width: 250px; top: 0; bottom: 0; z-index: 100; padding-top: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        
        .nav-link { color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; transition: all 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background-color: #eef2ff; color: var(--primary); }
        .nav-link i { width: 20px; text-align: center; } 
        
        .content-card { background: white; border-radius: 12px; border: none; padding: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); }
        .stat-box { padding: 20px; border-radius: 12px; color: white; }

        /* Mode Cetak/Print */
        @media print { 
            .sidebar, .filter-box, .btn-print { display: none !important; } 
            .main-content { margin-left: 0; padding: 0; } 
            .content-card { box-shadow: none; border: 1px solid #eee; } 
            body { background-color: white; }
        }
        
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>

    <div class="sidebar d-none d-md-block px-3">
        <div class="px-2 mb-4 d-flex align-items-center justify-content-center gap-2 text-primary">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo" style="height: 60px; width: auto; object-fit: contain;">
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?= url_is('admin/dashboard') ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <a class="nav-link <?= url_is('admin/orders*') ? 'active' : '' ?>" href="<?= site_url('admin/orders') ?>">
                <i class="fas fa-shopping-cart"></i> Data Pesanan
            </a>
            
            <a class="nav-link justify-content-between <?= url_is('admin/verification*') ? 'active' : '' ?>" href="<?= site_url('admin/verification') ?>">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-double"></i> Verifikasi
                </div>
                <?php if (isset($needVerif) && $needVerif > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $needVerif ?></span>
                <?php endif; ?>
            </a>
            
            <hr class="my-2">
            
            <a class="nav-link <?= url_is('admin/products*') ? 'active' : '' ?>" href="<?= site_url('admin/products') ?>">
                <i class="fas fa-box"></i> Produk
            </a>
            
            <a class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>">
                <i class="fas fa-users"></i> Users
            </a>
            
            <a class="nav-link <?= url_is('admin/laporan*') ? 'active' : '' ?>" href="<?= site_url('admin/laporan') ?>">
                <i class="fas fa-file-alt"></i> Laporan
            </a>
            
            <hr>
            
            <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0 text-dark">Laporan Penjualan</h3>
                <p class="text-muted small">Ringkasan transaksi yang telah lunas (Paid).</p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 btn-print">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </button>
        </div>

        <div class="content-card mb-4 filter-box">
            <form method="GET" action="<?= site_url('admin/laporan') ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Metode Bayar</label>
                    <select name="payment_method" class="form-select">
                        <option value="">Semua Metode</option>
                        <?php foreach ($paymentMethods as $method): ?>
                            <option value="<?= esc($method) ?>" <?= $paymentMethod === $method ? 'selected' : '' ?>>
                                <?= strtoupper(esc($method)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fas fa-filter me-2"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="stat-box bg-primary shadow-sm">
                    <div class="small opacity-75">Total Pendapatan (Lunas)</div>
                    <h2 class="fw-bold m-0">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-box bg-dark shadow-sm">
                    <div class="small opacity-75">Total Pesanan Berhasil</div>
                    <h2 class="fw-bold m-0"><?= $totalOrders ?> Transaksi</h2>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr class="small text-uppercase text-muted">
                            <th>ID Order</th>
                            <th>Tgl Transaksi</th>
                            <th>Nama Customer</th>
                            <th>Metode Bayar</th>
                            <th class="text-end">Total Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $r['id'] ?></td>
                            <td>
                                <div class="small fw-bold"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                                <div class="text-muted small"><?= date('H:i', strtotime($r['created_at'])) ?> WIB</div>
                            </td>
                            <td class="fw-bold text-dark"><?= esc($r['customer_name']) ?></td>
                            <td>
                                <?php if ($r['address'] == 'POS TRANSAKSI'): ?>
                                    <span class="badge bg-dark">KASIR</span>
                                <?php endif; ?>
                                <span class="badge bg-light text-dark border"><?= strtoupper(esc($r['payment_method'])) ?></span>
                            </td>
                            <td class="text-end fw-bold text-dark fs-6">Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 d-block mb-3 opacity-25"></i>
                                Tidak ada transaksi lunas pada periode dan filter yang dipilih.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>