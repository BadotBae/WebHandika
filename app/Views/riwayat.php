<?php
// Helper pemformatan dan status label di dalam view
function formatRupiah($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

function statusBadge($payment, $shipping) {
    if ($payment === 'cancelled') {
        return ['bg' => '#dc3545', 'label' => 'Dibatalkan'];
    }
    $ship = strtolower($shipping ?? '');
    if ($ship === 'selesai' || $ship === 'sampai') {
        return ['bg' => '#198754', 'label' => 'Selesai'];
    }
    if ($ship === 'dikirim') {
        return ['bg' => '#0dcaf0', 'label' => 'Dikirim'];
    }
    if ($ship === 'diproses') {
        if ($payment === 'pending_cod' || stripos($payment, 'cod') !== false) {
            return ['bg' => '#0d6efd', 'label' => 'COD - Diproses'];
        }
        return ['bg' => '#0d6efd', 'label' => 'Diproses'];
    }
    return match($payment) {
        'paid'        => ['bg' => '#198754', 'label' => 'Lunas - Menunggu Proses'],
        'pending_cod' => ['bg' => '#fd7e14', 'label' => 'COD – Menunggu Konfirmasi'],
        'unpaid'      => ['bg' => '#dc3545', 'label' => 'Belum Bayar'],
        'pending'     => ['bg' => '#ffc107', 'label' => 'Menunggu Verifikasi'],
        default       => ['bg' => '#6c757d', 'label' => ucfirst($payment)],
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Riwayat Order – Handika Helmet Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; --bg-body: #ffffff; --danger: #e63946; }
        body { font-family: "Outfit", sans-serif; background: #f8f9fa; color: #2b2d42; }
        
        /* --- NAVBAR STYLES --- */
        .navbar-njs { background-color:#ffffff; padding:15px 0; border-bottom:1px solid #f1f1f1; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .nav-link-center { color:#000; font-weight:500; font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px; padding:0.5rem 1rem !important; transition:all 0.3s ease; }
        .nav-link-center:hover { color:var(--danger); }
        .icon-action { color:#000; font-size:1.25rem; transition:all 0.2s ease; cursor:pointer; text-decoration:none; position:relative; }
        .icon-action:hover { color:var(--danger); }
        .cart-badge-njs { position:absolute; top:-6px; right:-8px; background:var(--danger); color:white; border: 2px solid white; border-radius:50px; font-size:0.65rem; padding: 0.3em 0.5em; line-height: 1; }
        .dropdown-toggle.icon-action::after { display: none; }
        
        /* Order Cards */
        .order-card { background: #fff; border-radius: 16px; border: 1px solid #f0f0f0; overflow: hidden; transition: box-shadow .2s; }
        .order-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .order-header { background: #fafafa; padding: 16px 20px; border-bottom: 1px solid #f0f0f0; }
        .order-body { padding: 16px 20px; }
        .order-footer { padding: 12px 20px; border-top: 1px solid #f0f0f0; background: #fafafa; }
        .thumb-wrap { width: 56px; height: 56px; border-radius: 10px; overflow: hidden; background: #f5f5f5; flex-shrink: 0; }
        .thumb-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .payment-box { background: #f8f9fa; border-radius: 10px; padding: 14px 16px; font-size: .88rem; }
        .payment-box .label { color: #6c757d; font-size: .78rem; margin-bottom: 2px; }
        .payment-box .value { font-weight: 600; color: #2b2d42; word-break: break-all; }
        .modal-content { border-radius: 20px; border: none; }
        .btn-batal-order { border-color: #dc3545; color: #dc3545; }
        .btn-batal-order:hover { background: #dc3545; color: #fff; }
        .empty-state { text-align: center; padding: 80px 20px; }
        @media (max-width: 576px) {
            .order-header { padding: 12px 14px; }
            .order-body  { padding: 12px 14px; }
            .order-footer{ padding: 10px 14px; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ========================= NAVBAR START ========================= -->
<nav class="navbar navbar-expand-lg navbar-njs sticky-top">
    <div class="container-fluid px-4 px-lg-5">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center me-0" href="<?= site_url('/') ?>">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo Toko" style="height:45px; width:auto;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span class="fw-bold fs-4 text-dark" style="display: none; letter-spacing: 1px;">NJS HELMET</span>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCenter">
            <i class="fas fa-bars fs-4"></i>
        </button>
        
        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarCenter">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center mt-3 mt-lg-0">
                <li class="nav-item"><a class="nav-link nav-link-center" href="<?= site_url('/#produk-section') ?>">Shop</a></li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">New Arrivals</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-center dropdown-toggle" href="#" data-bs-toggle="dropdown">Shop by Categories</a>
                    <ul class="dropdown-menu border-0 shadow-sm text-center text-lg-start">
                        <?php if (!empty($categories)): foreach ($categories as $c): ?>
                            <li><a class="dropdown-item" href="<?= site_url('/?aside_category=' . urlencode($c)) ?>"><?= htmlspecialchars($c) ?></a></li>
                        <?php endforeach; endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">Customer Service</a></li>
            </ul>
            
            <!-- Navbar Actions -->
            <div class="d-flex align-items-center justify-content-center justify-content-lg-end gap-4 mt-3 mt-lg-0">
                <div class="d-flex align-items-center d-none d-lg-flex">
                    <img src="https://flagcdn.com/w20/id.png" alt="Indonesia" width="22" class="me-2 rounded-1 border border-light">
                    <span class="fw-semibold text-dark" style="font-size:0.9rem;">IDR</span>
                </div>
                
                <a href="#" class="icon-action" data-bs-toggle="collapse" data-bs-target="#searchBar"><i class="fas fa-search"></i></a>
                
                <a href="<?= site_url('wishlist') ?>" class="icon-action d-flex align-items-center">
                    <i class="far fa-heart"></i>
                    <?php if (!empty($wishlistCount) && $wishlistCount > 0): ?>
                        <span class="cart-badge-njs fw-bold"><?= $wishlistCount ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="<?= site_url('cart') ?>" class="icon-action d-flex align-items-center">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if (!empty($cartCount) && $cartCount > 0): ?>
                        <span class="cart-badge-njs fw-bold"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                
                <?php if ($user): ?>
                    <div class="dropdown">
                        <a href="#" class="icon-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="far fa-user"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><span class="dropdown-item-text fw-bold"><?= htmlspecialchars($user['name']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item active" href="<?= site_url('riwayat') ?>">Riwayat Order</a></li>
                            <?php if ($user['role_id'] == 1): ?>
                                <li><a class="dropdown-item" href="<?= site_url('admin/products') ?>">Kelola Produk (Admin)</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="dropdown">
                        <a href="#" class="icon-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="far fa-user"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="min-width:180px;">
                            <li><a class="dropdown-item fw-semibold" href="<?= site_url('auth/login') ?>"><i class="fas fa-sign-in-alt me-2 text-danger"></i>Login</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('auth/register') ?>"><i class="fas fa-user-plus me-2 text-secondary"></i>Register</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Search Bar Overlay -->
<div class="collapse bg-white border-bottom position-absolute w-100" id="searchBar" style="z-index:1030;">
    <div class="container py-3">
        <form method="get" action="<?= site_url('/') ?>" class="d-flex justify-content-center mx-auto" style="max-width:600px;">
            <div class="input-group">
                <input type="search" name="q" class="form-control rounded-start-pill bg-light border-0 px-4" placeholder="Ketik nama helm..." value="<?= htmlspecialchars($search ?? '') ?>">
                <button class="btn btn-dark rounded-end-pill px-4" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>
<!-- ========================= NAVBAR END ========================= -->

<main class="flex-grow-1">
<div class="container py-5" style="max-width: 860px;">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0" style="font-size:.85rem;">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active fw-semibold">Riwayat Order</li>
        </ol>
    </nav>

    <h4 class="fw-bold mb-4 d-flex align-items-center">
        Riwayat Order
        <?php if (count($orders) > 0): ?>
            <span class="badge rounded-pill ms-2 fw-normal" style="background:#f0f0f0; color:#555; font-size:.7rem;">
                <?= count($orders) ?> pesanan
            </span>
        <?php endif; ?>
    </h4>

    <?php if (empty($orders)): ?>
    <div class="empty-state">
        <i class="fas fa-box-open fa-4x text-muted opacity-25 d-block mb-4"></i>
        <h5 class="fw-semibold text-muted mb-2">Belum ada pesanan</h5>
        <p class="text-muted mb-4" style="font-size:.9rem;">Yuk belanja helm favoritmu sekarang!</p>
        <a href="<?= site_url('/') ?>" class="btn btn-dark rounded-pill px-5">Mulai Belanja</a>
    </div>

    <?php else: ?>
    <div class="d-flex flex-column gap-4">
    <?php foreach ($orders as $order):
        $badge   = statusBadge($order['payment_status'], $order['shipping_status'] ?? '');
        $items   = $orderItems[$order['id']] ?? [];
        $isQR    = str_starts_with($order['payment_info'], 'http');
        $isVA    = preg_match('/^\d{8,}$/', $order['payment_info'] ?? '');
        $orderId = str_pad($order['id'], 5, '0', STR_PAD_LEFT);
        
        $detailData = htmlspecialchars(json_encode([
            'id'             => $orderId,
            'date'           => date('d M Y, H:i', strtotime($order['created_at'])),
            'status'         => $badge['label'],
            'status_bg'      => $badge['bg'],
            'total'          => formatRupiah($order['total']),
            'address'        => $order['address'],
            'method'         => $order['payment_method'],
            'info'           => $order['payment_info'],
            'is_qr'          => $isQR,
            'is_va'          => (bool)$isVA,
            'items'          => array_map(fn($it) => [
                'name'  => $it['name'] ?? 'Produk dihapus',
                'img'   => base_url($it['img'] ?? 'img/placeholder.png'),
                'qty'   => $it['qty'],
                'price' => formatRupiah($it['price']),
                'sub'   => formatRupiah($it['price'] * $it['qty']),
            ], $items),
        ]), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="order-card">
        <div class="order-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <span class="fw-bold" style="font-size:.95rem; color:#111;">
                    <i class="fas fa-hashtag me-1 text-muted" style="font-size:.8rem;"></i>ORD-<?= $orderId ?>
                </span>
                <span class="ms-3 text-muted" style="font-size:.8rem;">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WIB
                </span>
            </div>
            <span class="badge rounded-pill px-3 py-2" style="background:<?= $badge['bg'] ?>; font-size:.75rem; letter-spacing:.3px;">
                <?= $badge['label'] ?>
            </span>
        </div>

        <div class="order-body">
            <div class="d-flex flex-column gap-3">
                <?php foreach ($items as $item): ?>
                <div class="d-flex align-items-center gap-3">
                    <div class="thumb-wrap">
                        <img src="<?= base_url(htmlspecialchars($item['img'] ?? 'img/placeholder.png')) ?>" alt="Product">
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:.88rem; color:#111;">
                            <?= htmlspecialchars($item['name'] ?? 'Produk dihapus') ?>
                        </div>
                        <div class="text-muted" style="font-size:.8rem;">
                            <?= $item['qty'] ?> × <?= formatRupiah($item['price']) ?>
                        </div>
                    </div>
                    <div class="fw-semibold text-end" style="font-size:.88rem; white-space:nowrap;">
                        <?= formatRupiah($item['price'] * $item['qty']) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <hr class="my-3" style="border-color:#f0f0f0;">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size:.85rem;">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    <?= htmlspecialchars(mb_strimwidth($order['address'], 0, 55, '…')) ?>
                </span>
                <div class="text-end">
                    <div class="text-muted" style="font-size:.75rem;">Total Pembayaran</div>
                    <div class="fw-bold" style="font-size:1rem; color:#e63946;">
                        <?= formatRupiah($order['total']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="text-muted" style="font-size:.82rem;">
                <i class="fas fa-credit-card me-1"></i> <?= htmlspecialchars($order['payment_method']) ?>
            </span>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-dark rounded-pill px-3" style="font-size:.82rem;" data-order="<?= $detailData ?>" onclick="openDetail(this)">
                    <i class="fas fa-eye me-1"></i> Detail
                </button>

                <?php 
                $shipStat = strtolower($order['shipping_status'] ?? '');
                $payStat  = $order['payment_status'];
                $bolehBatal = in_array($payStat, ['pending_cod', 'paid', 'unpaid', 'pending'])
                           && !in_array($shipStat, ['diproses','dikirim','selesai','sampai','cancelled']);
                           
                if ($bolehBatal && $payStat !== 'cancelled'):
                ?>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold btn-batal-order" style="font-size:.82rem;"
                        onclick="konfirmasiBatal(<?= $order['id'] ?>, '<?= addslashes($badge['label']) ?>')">
                    <i class="fas fa-times-circle me-1"></i> Batalkan
                </button>
                <?php endif; ?>

                <?php if ($shipStat === 'dikirim' && !in_array($payStat, ['cancelled', 'unpaid', 'pending'])): ?>
                <form method="POST" action="<?= site_url('riwayat') ?>" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin pesanan ini sudah diterima dengan baik?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="action" value="terima_pesanan">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" style="font-size:.82rem;">
                        <i class="fas fa-check-circle me-1"></i> Pesanan Diterima
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</main>

<!-- ========================= FOOTER START ========================= -->
<footer class="bg-dark text-white pt-5 pb-4 mt-auto">
    <div class="container px-4 px-lg-5">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <img src="<?= base_url('img/image.png') ?>" alt="Logo" style="height:40px; filter:brightness(0) invert(1);" class="mb-3">
                <p style="font-size:0.9rem; color:#bbb;">Handika Helmet Store - Toko helm terpercaya dengan kualitas terbaik dan harga terjangkau.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white fs-5"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white fs-5"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white fs-5"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="text-white fs-5"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3 text-uppercase" style="letter-spacing:1px;">Information</h6>
                <ul class="list-unstyled" style="font-size:0.9rem;">
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">About Us</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">Contact Us</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">Terms & Conditions</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3 text-uppercase" style="letter-spacing:1px;">Customer Care</h6>
                <ul class="list-unstyled" style="font-size:0.9rem;">
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">Shipping & Returns</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">Size Guide</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none" style="color:#bbb;">Track Order</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3 text-uppercase" style="letter-spacing:1px;">Contact</h6>
                <ul class="list-unstyled" style="font-size:0.9rem; color:#bbb;">
                    <li class="mb-2 d-flex"><i class="fas fa-map-marker-alt mt-1 me-2 text-white"></i> Jl. Siliwangi, Cijoho, Kec. Kuningan</li>
                    <li class="mb-2 d-flex"><i class="fas fa-phone mt-1 me-2 text-white"></i> +62 8317 2994 917</li>
                    <li class="mb-2 d-flex"><i class="fas fa-envelope mt-1 me-2 text-white"></i> handikahelmet@gmail.com</li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="border-color:rgba(255,255,255,0.1);">
        <div class="text-center" style="font-size:0.85rem; color:#bbb;">
            &copy; <span id="year"></span> Handika Helmet Store. All rights reserved.
        </div>
    </div>
</footer>

<!-- MODAL KONFIRMASI BATALKAN PESANAN -->
<div class="modal fade" id="batalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:44px;height:44px;background:#fff0f0;">
                        <i class="fas fa-times-circle" style="color:#dc3545;font-size:1.3rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size:1rem;">Batalkan Pesanan?</h5>
                        <small class="text-muted" id="batal-order-label">Order #—</small>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="p-3 rounded-3 mb-3" style="background:#fff8f0;border:1.5px solid #ffd8b0;">
                    <div class="d-flex gap-2">
                        <i class="fas fa-exclamation-triangle text-warning mt-1 flex-shrink-0"></i>
                        <p class="mb-0 text-muted" style="font-size:.85rem;">
                            Pesanan yang dibatalkan <strong>tidak dapat dipulihkan</strong>. Stok akan dikembalikan secara otomatis.
                        </p>
                    </div>
                </div>
                <label class="form-label fw-semibold" style="font-size:.85rem;">Alasan pembatalan (opsional)</label>
                <div class="d-flex flex-column gap-2 mb-3">
                    <label class="d-flex align-items-center gap-2 p-2 rounded-3 border"><input type="radio" name="alasan_radio" value="Berubah pikiran" class="form-check-input mt-0"> Berubah pikiran</label>
                    <label class="d-flex align-items-center gap-2 p-2 rounded-3 border"><input type="radio" name="alasan_radio" value="Salah pilih produk" class="form-check-input mt-0"> Salah pilih produk / ukuran</label>
                    <label class="d-flex align-items-center gap-2 p-2 rounded-3 border"><input type="radio" name="alasan_radio" value="Ingin ganti alamat" class="form-check-input mt-0"> Ingin ganti alamat</label>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-fill" data-bs-dismiss="modal">Tidak Jadi</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 flex-fill fw-bold" onclick="submitBatal()">
                    <i class="fas fa-times-circle me-1"></i>Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form Submit Batalkan -->
<form method="POST" action="<?= site_url('riwayat') ?>" id="formBatal" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="batalkan_pesanan">
    <input type="hidden" name="order_id" id="batalOrderId">
    <input type="hidden" name="alasan" id="batalAlasan">
</form>

<!-- MODAL DETAIL -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="fw-bold mb-0" id="modal-order-id">Detail Pesanan</h5>
                    <small class="text-muted" id="modal-order-date"></small>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge rounded-pill px-3 py-2" id="modal-status-badge" style="font-size:.8rem;"></span>
                    <span class="text-muted" style="font-size:.82rem;" id="modal-method"></span>
                </div>
                <div class="fw-semibold mb-2" style="font-size:.85rem; color:#888; text-transform:uppercase;">Produk</div>
                <div id="modal-items" class="d-flex flex-column gap-2 mb-3"></div>
                <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-3">
                    <span class="fw-semibold" style="font-size:.9rem;">Total Pembayaran</span>
                    <span class="fw-bold" id="modal-total" style="font-size:1rem; color:#e63946;"></span>
                </div>
                <div class="payment-box mb-3">
                    <div class="label"><i class="fas fa-map-marker-alt me-1"></i>Alamat Pengiriman</div>
                    <div class="value" id="modal-address"></div>
                </div>
                <div id="modal-payment-section"></div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-dark rounded-pill w-100 py-2 fw-semibold" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('year').textContent = new Date().getFullYear();

    function openDetail(btnElement) {
        const data = JSON.parse(btnElement.getAttribute('data-order'));
        document.getElementById('modal-order-id').textContent  = '#ORD-' + data.id;
        document.getElementById('modal-order-date').textContent = data.date + ' WIB';

        const badge = document.getElementById('modal-status-badge');
        badge.textContent = data.status; badge.style.background = data.status_bg;
        document.getElementById('modal-method').textContent = data.method;
        document.getElementById('modal-total').textContent   = data.total;
        document.getElementById('modal-address').textContent = data.address;

        const itemsEl = document.getElementById('modal-items');
        itemsEl.innerHTML = '';
        data.items.forEach(it => {
            itemsEl.innerHTML += `
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;border-radius:8px;overflow:hidden;background:#f5f5f5;flex-shrink:0;">
                        <img src="${it.img}" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='<?= base_url('img/placeholder.png') ?>'">
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:.85rem;">${it.name}</div>
                        <div class="text-muted" style="font-size:.78rem;">${it.qty} × ${it.price}</div>
                    </div>
                    <div class="fw-semibold" style="font-size:.85rem;white-space:nowrap;">${it.sub}</div>
                </div>`;
        });

        const paySection = document.getElementById('modal-payment-section');
        if (data.is_qr) {
            paySection.innerHTML = `
                <div class="payment-box text-center">
                    <div class="label mb-2"><i class="fas fa-qrcode me-1"></i>QR Code Pembayaran</div>
                    <img src="${data.info}" alt="QR Code" style="width:180px;height:180px;border-radius:10px;">
                </div>`;
        } else if (data.is_va) {
            paySection.innerHTML = `
                <div class="payment-box">
                    <div class="label"><i class="fas fa-university me-1"></i>Nomor Virtual Account</div>
                    <div class="value d-flex align-items-center gap-2 mt-1">
                        <span id="va-number" style="font-size:1.05rem;letter-spacing:2px;">${data.info}</span>
                    </div>
                </div>`;
        } else if (data.info && data.info !== '-') {
            paySection.innerHTML = `<div class="payment-box"><div class="label">Info Pembayaran</div><div class="value mt-1">${data.info}</div></div>`;
        } else {
            paySection.innerHTML = '';
        }

        new bootstrap.Modal(document.getElementById('orderDetailModal')).show();
    }

    function konfirmasiBatal(orderId, statusLabel) {
        document.getElementById('batalOrderId').value = orderId;
        document.getElementById('batal-order-label').textContent = 'Status: ' + statusLabel;
        new bootstrap.Modal(document.getElementById('batalModal')).show();
    }

    function submitBatal() {
        const alasanEl = document.querySelector('input[name="alasan_radio"]:checked');
        document.getElementById('batalAlasan').value = alasanEl ? alasanEl.value : '';
        document.getElementById('formBatal').submit();
    }
</script>

<?php if (session()->getFlashdata('flashMsg')): ?>
<script>
    Swal.fire({
        icon: '<?= session()->getFlashdata('flashType') === 'error' ? 'error' : 'success' ?>',
        title: '<?= session()->getFlashdata('flashType') === 'error' ? 'Gagal' : 'Berhasil' ?>',
        text: '<?= session()->getFlashdata('flashMsg') ?>',
        timer: 3000,
        showConfirmButton: <?= session()->getFlashdata('flashType') === 'error' ? 'true' : 'false' ?>
    });
</script>
<?php endif; ?>

</body>
</html>