<?php
// Helper untuk Badge Status Pembayaran & Pengiriman (Aman diletakkan di View CI4)
if (!function_exists('paymentBadge')) {
    function paymentBadge($status) {
        return match($status) {
            'paid'        => '<span class="badge bg-success">Lunas</span>',
            'unpaid'      => '<span class="badge bg-warning text-dark">Belum Bayar</span>',
            'pending'     => '<span class="badge bg-warning text-dark">Pending</span>',
            'pending_cod' => '<span class="badge bg-secondary">Pending COD</span>',
            'cancelled'   => '<span class="badge bg-danger">Dibatalkan</span>',
            default       => '<span class="badge bg-light text-dark border">' . esc($status) . '</span>',
        };
    }
}

if (!function_exists('shippingBadge')) {
    function shippingBadge($status) {
        return match($status) {
            'selesai'        => '<span class="badge bg-success">Selesai</span>',
            'dikirim'        => '<span class="badge bg-info text-dark">Dikirim</span>',
            'diproses'       => '<span class="badge bg-primary">Diproses</span>',
            'belum_dikirim'  => '<span class="badge bg-secondary">Belum Dikirim</span>',
            default          => '<span class="badge bg-light text-dark border">' . esc($status) . '</span>',
        };
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Detail Order #<?= esc($order['id']) ?></title>
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
        .content-card { background: white; border-radius: 12px; border: none; padding: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); }
        .img-product { width: 55px; height: 55px; object-fit: cover; border-radius: 10px; }
        .info-label { font-size: 0.75rem; font-weight: 600; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
        .info-value { font-weight: 500; color: #2b2d42; }
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; padding: 15px; } }

        @media print {
            .sidebar, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .content-card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
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
            <div style="display:flex;align-items:center;gap:10px;"><i class="fas fa-check-double"></i> Verifikasi</div>
            <?php if(isset($needVerif) && $needVerif > 0): ?><span class="badge bg-danger rounded-pill"><?= $needVerif ?></span><?php endif; ?>
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

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <a href="<?= site_url('admin/orders') ?>" class="btn btn-sm btn-light border rounded-pill mb-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <h3 class="fw-bold m-0">Detail Order <span class="text-primary">#<?= esc($order['id']) ?></span></h3>
            <small class="text-muted"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill no-print">
            <i class="fas fa-print me-2"></i> Cetak Invoice
        </button>
    </div>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-<?= session()->getFlashdata('msgType') ?? 'info' ?> alert-dismissible fade show no-print">
            <?= session()->getFlashdata('msg') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="content-card mb-4">
                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-user me-2"></i>Info Pelanggan</h6>
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="info-label">Nama</div>
                        <div class="info-value"><?= esc($order['user_name']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= esc($order['user_email']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-label">Sumber</div>
                        <div class="info-value">
                            <?php if($order['address'] == 'POS TRANSAKSI'): ?>
                                <span class="badge bg-dark">KASIR</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">WEB/ONLINE</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if($order['address'] != 'POS TRANSAKSI'): ?>
                    <div class="col-12">
                        <div class="info-label">Alamat Pengiriman</div>
                        <div class="info-value"><?= nl2br(esc($order['address'])) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($order['shipping_service'])): ?>
                    <div class="col-sm-4">
                        <div class="info-label">Jasa Kirim</div>
                        <div class="info-value"><?= esc($order['shipping_service']) ?></div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-label">Ongkos Kirim</div>
                        <div class="info-value">Rp <?= number_format($order['shipping_cost'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="content-card">
                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-box me-2"></i>Produk Dipesan</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase">
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= base_url(esc($item['product_img'])) ?>" class="img-product"
                                             onerror="this.src='https://via.placeholder.com/55?text=No+Img'">
                                        <div>
                                            <div class="fw-bold small"><?= esc($item['product_name']) ?></div>
                                            <?php if(!empty($item['colour']) || !empty($item['size'])): ?>
                                                <span class="badge bg-light text-secondary border" style="font-size:0.7rem;">
                                                    <?= esc($item['colour']) ?> / <?= esc($item['size']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-bold"><?= $item['qty'] ?></td>
                                <td class="text-end text-muted">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top">
                            <?php if(isset($order['shipping_cost']) && $order['shipping_cost'] > 0): ?>
                            <tr>
                                <td colspan="3" class="text-end text-muted small">Ongkos Kirim</td>
                                <td class="text-end text-muted">Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary fs-6">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="content-card mb-4">
                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-credit-card me-2"></i>Pembayaran</h6>
                <div class="mb-2">
                    <div class="info-label">Metode</div>
                    <div class="info-value">
                        <span class="badge border text-dark"><?= esc($order['payment_method']) ?></span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Status</div>
                    <div><?= paymentBadge($order['payment_status']) ?></div>
                </div>
                <?php if(!empty($order['payment_info']) && $order['payment_status'] != 'paid'): ?>
                <div class="mb-3">
                    <div class="info-label">Info Pembayaran</div>
                    <div class="info-value small"><?= nl2br(esc($order['payment_info'])) ?></div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('admin/orders/detail/' . $order['id']) ?>" class="no-print">
                    <?= csrf_field() ?>
                    <hr class="my-3">
                    <div class="mb-2">
                        <label class="info-label d-block mb-1">Ubah Status Pembayaran</label>
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="paid"        <?= $order['payment_status'] == 'paid'        ? 'selected' : '' ?>>Lunas</option>
                            <option value="unpaid"      <?= $order['payment_status'] == 'unpaid'      ? 'selected' : '' ?>>Belum Bayar</option>
                            <option value="pending"     <?= $order['payment_status'] == 'pending'     ? 'selected' : '' ?>>Pending</option>
                            <option value="pending_cod" <?= $order['payment_status'] == 'pending_cod' ? 'selected' : '' ?>>Pending COD</option>
                            <option value="cancelled"   <?= $order['payment_status'] == 'cancelled'   ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" name="update_payment" value="1" class="btn btn-primary btn-sm w-100 rounded-pill">
                        <i class="fas fa-save me-1"></i> Simpan Pembayaran
                    </button>
                </form>
            </div>

            <div class="content-card">
                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-truck me-2"></i>Pengiriman</h6>
                <div class="mb-3">
                    <div class="info-label">Status</div>
                    <div><?= shippingBadge($order['shipping_status']) ?></div>
                </div>

                <form method="POST" action="<?= site_url('admin/orders/detail/' . $order['id']) ?>" class="no-print">
                    <?= csrf_field() ?>
                    <hr class="my-3">
                    <div class="mb-2">
                        <label class="info-label d-block mb-1">Ubah Status Pengiriman</label>
                        <select name="shipping_status" class="form-select form-select-sm">
                            <option value="belum_dikirim" <?= $order['shipping_status'] == 'belum_dikirim' ? 'selected' : '' ?>>Belum Dikirim</option>
                            <option value="diproses"      <?= $order['shipping_status'] == 'diproses'      ? 'selected' : '' ?>>Diproses</option>
                            <option value="dikirim"       <?= $order['shipping_status'] == 'dikirim'       ? 'selected' : '' ?>>Dikirim</option>
                            <option value="selesai"       <?= $order['shipping_status'] == 'selesai'       ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <button type="submit" name="update_shipping" value="1" class="btn btn-primary btn-sm w-100 rounded-pill">
                        <i class="fas fa-save me-1"></i> Simpan Pengiriman
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>