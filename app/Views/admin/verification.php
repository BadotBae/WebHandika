<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Verifikasi Pembayaran</title>
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
        
        .content-card { background: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); border: none; padding: 25px; }
        .table thead th { background-color: #fcfcfd; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-top: none; }
        
        .payment-info-box { background: #f8f9fa; padding: 10px; border-radius: 8px; font-size: 0.8rem; border: 1px dashed #ccc; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <div class="sidebar d-none d-md-block px-3">
        <div class="px-2 mb-4 d-flex align-items-center justify-content-center gap-2 text-primary">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo" style="height: 60px; width: auto; object-fit: contain;">
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?= url_is('admin/dashboard') ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
            <a class="nav-link <?= url_is('admin/orders*') ? 'active' : '' ?>" href="<?= site_url('admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Data Pesanan</a>
            <a class="nav-link <?= url_is('admin/verification*') ? 'active' : '' ?> justify-content-between" href="<?= site_url('admin/verification') ?>">
                <div class="d-flex gap-2 align-items-center"><i class="fas fa-check-double"></i> Verifikasi</div>
                <?php if (isset($needVerif) && $needVerif > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $needVerif ?></span>
                <?php endif; ?>
            </a>
            <hr class="my-2">
            <a class="nav-link <?= url_is('admin/products*') ? 'active' : '' ?>" href="<?= site_url('admin/products') ?>"><i class="fas fa-box"></i> Produk</a>
            <a class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>"><i class="fas fa-users"></i> Users</a>
            <a class="nav-link <?= url_is('admin/laporan*') ? 'active' : '' ?>" href="<?= site_url('admin/laporan') ?>"><i class="fas fa-file-alt"></i> Laporan</a>
            <hr>
            <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="mb-4">
            <h3 class="fw-bold m-0">Verifikasi Pembayaran</h3>
            <small class="text-muted">Proses dan konfirmasi pesanan pelanggan yang masuk sebelum melakukan pengiriman.</small>
        </div>

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="alert alert-<?= session()->getFlashdata('msgType') ?? 'info' ?> alert-dismissible fade show" role="alert">
                <i class="fas <?= session()->getFlashdata('msgType') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i> <?= session()->getFlashdata('msg') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <?php if (empty($pendingOrders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-double fs-1 text-success opacity-50 mb-3 d-block"></i>
                    <h5 class="fw-bold text-muted">Semua Pesanan Sudah Diproses</h5>
                    <p class="text-muted small">Tidak ada pesanan yang menunggu verifikasi saat ini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th style="width: 250px;">Info / Catatan</th>
                                <th>Total Nilai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingOrders as $o): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">#<?= $o['id'] ?></div>
                                    <small class="fw-bold"><?= esc($o['user_name']) ?></small>
                                </td>
                                <td>
                                    <div class="small"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
                                    <div class="text-muted small"><i class="far fa-clock"></i> <?= date('H:i', strtotime($o['created_at'])) ?> WIB</div>
                                </td>
                                <td>
                                    <?php if (stripos($o['payment_method'], 'cod') !== false): ?>
                                        <span class="badge bg-info text-dark">Bayar di Tempat (COD)</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= strtoupper(esc($o['payment_method'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="payment-info-box">
                                        <?= !empty($o['payment_info']) ? nl2br(esc($o['payment_info'])) : '<span class="text-muted fst-italic">Tidak ada catatan</span>' ?>
                                    </div>
                                </td>
                                <td class="fw-bold text-dark fs-5">
                                    Rp <?= number_format($o['total'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="<?= site_url('admin/verification/process') ?>" class="d-flex flex-column gap-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success fw-bold" onclick="return confirm('Apakah Anda yakin ingin menyetujui pesanan ini? Status akan berubah menjadi Lunas.')">
                                            <i class="fas fa-check me-1"></i> Terima
                                        </button>
                                        
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat diurungkan.')">
                                            <i class="fas fa-times me-1"></i> Tolak
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>