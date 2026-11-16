<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Data Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; --bg-body: #f8f9fa; }
        body { font-family: "Outfit", sans-serif; background-color: var(--bg-body); color: #2b2d42; }
        
        .sidebar { background: white; min-height: 100vh; border-right: 1px solid #eee; position: fixed; width: 250px; top: 0; bottom: 0; z-index: 100; padding-top: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        
        .nav-link { color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; transition: all 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background-color: #eef2ff; color: var(--primary); }
        .nav-link i { width: 20px; text-align: center; }
        
        .content-card { background: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); border: none; padding: 25px; }
        .search-input { border-radius: 20px; padding-left: 2.5rem; border: 1px solid #dee2e6; }
        .search-input:focus { box-shadow: 0 0 0 3px rgba(67,97,238,0.15); border-color: var(--primary); }
        .search-wrapper { position: relative; }
        .search-wrapper .fa-search { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd; pointer-events: none; }
        .table thead th { background-color: #fcfcfd; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-top: none; }
        
        .badge-status { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

 <div class="sidebar d-none d-md-block px-3">
    <div class="px-2 mb-4 d-flex align-items-center justify-content-center gap-2 text-primary">
    <!-- Penyesuaian path gambar dengan base_url() -->
    <img src="<?= base_url('img/logo.png') ?>" alt="Logo" style="height: 60px; width: auto; object-fit: contain;">
</div>
        <nav class="nav flex-column">
            <!-- Penyesuaian routing CI4 -->
            <a class="nav-link" href="<?= site_url('admin') ?>"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link active" href="<?= site_url('admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Data Pesanan</a>
            
            <a class="nav-link justify-content-between" href="<?= site_url('admin/verification') ?>">
                <div class="d-flex gap-2 align-items-center"><i class="fas fa-check-double"></i> Verifikasi</div>
                <?php if($needVerif > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $needVerif ?></span>
                <?php endif; ?>
            </a>
            
            <hr class="my-2">
            <a class="nav-link" href="<?= site_url('admin/products') ?>"><i class="fas fa-box"></i> Produk</a>
            <a class="nav-link" href="<?= site_url('admin/users') ?>"><i class="fas fa-users"></i> Users</a>
            <a class="nav-link" href="<?= site_url('admin/laporan') ?>"><i class="fas fa-file-alt"></i> Laporan</a>
            <hr>
            <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold m-0">Data Pesanan</h3>
                <small class="text-muted">Total <?= esc($totalOrders) ?> pesanan ditemukan.</small>
            </div>
            <!-- Form mengarah ke method Controller dengan site_url -->
            <form action="<?= site_url('admin/orders') ?>" method="GET" class="d-flex gap-2">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control search-input" placeholder="Cari ID, Nama, Status, Pengiriman..." value="<?= esc($search) ?>">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-3"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID Order</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total Belanja</th>
                            <th>Metode / Pembayaran</th>
                            <th>Pengiriman</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= esc($o['id']) ?></td>
                            
                            <td>
                                <div class="fw-bold"><?= esc($o['user_name']) ?></div>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <?= $o['address'] == 'POS TRANSAKSI' ? '<i class="fas fa-store me-1"></i>Kasir' : '<i class="fas fa-globe me-1"></i>Web' ?>
                                </small>
                            </td>
                            
                            <td>
                                <div class="small fw-bold"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
                                <div class="text-muted small"><?= date('H:i', strtotime($o['created_at'])) ?> WIB</div>
                            </td>
                            
                            <td class="fw-bold text-dark">Rp <?= number_format($o['total'], 0, ',', '.') ?></td>
                            
                            <td>
                                <div class="mb-1 small text-muted fw-bold"><?= strtoupper($o['payment_method']) ?></div>
                                <?php if($o['payment_status'] == 'paid'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success badge-status">LUNAS</span>
                                <?php elseif(in_array($o['payment_status'], ['unpaid', 'pending_cod', 'pending'])): ?>
                                    <span class="badge bg-warning text-dark badge-status">MENUNGGU</span>
                                <?php elseif($o['payment_status'] == 'cancelled'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger badge-status">BATAL</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary badge-status"><?= strtoupper(esc($o['payment_status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php 
                                    $ss = strtolower($o['shipping_status']);
                                    $shipClass = 'text-muted';
                                    if ($ss == 'diproses') $shipClass = 'text-warning';
                                    if ($ss == 'dikirim') $shipClass = 'text-primary';
                                    if ($ss == 'sampai' || $ss == 'selesai') $shipClass = 'text-success';
                                ?>
                                <small class="fw-bold <?= $shipClass ?>">
                                    <i class="fas fa-truck me-1"></i> <?= str_replace('_', ' ', strtoupper($ss)) ?>
                                </small>
                            </td>
                            
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <!-- Penyesuaian Link Detail menggunakan CI4 Segment Routing -->
                                    <a href="<?= site_url('admin/order_detail/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Lihat Detail">
                                        Detail
                                    </a>
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Update Pengiriman">
                                            <i class="fas fa-truck me-1"></i> Pengiriman
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.85rem;">
                                            <li><h6 class="dropdown-header">Update Status Pengiriman</h6></li>
                                            <!-- Penyesuaian Link Update menggunakan CI4 Segment Routing -->
                                            <li>
                                                <a class="dropdown-item py-2" href="<?= site_url('admin/update_shipping/' . $o['id'] . '/diproses') ?>">
                                                    <i class="fas fa-box text-warning me-2"></i> Diproses
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="<?= site_url('admin/update_shipping/' . $o['id'] . '/dikirim') ?>">
                                                    <i class="fas fa-shipping-fast text-primary me-2"></i> Dikirim
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="<?= site_url('admin/update_shipping/' . $o['id'] . '/selesai') ?>">
                                                    <i class="fas fa-check-circle text-success me-2"></i> Selesai
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-1 d-block mb-3 opacity-25"></i>
                                Tidak ada data pesanan yang ditemukan.
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