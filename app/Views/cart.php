<?php
// Helper format rupiah
function formatRupiah($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Keranjang – Handika Helmet Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; }
        body { font-family: "Outfit", sans-serif; background: #f8f9fa; color: #2b2d42; }

        /* NAV */
        .navbar-njs { background-color: #ffffff; padding: 15px 0; border-bottom: 1px solid #f1f1f1; box-shadow: none; }
        .nav-link-center { color: #000; font-weight: 500; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.5rem 1rem !important; transition: all 0.3s ease; }
        .nav-link-center:hover { color: var(--primary); }
        .icon-action { color: #000; font-size: 1.25rem; transition: all 0.2s ease; cursor: pointer; text-decoration: none; position: relative; }
        .icon-action:hover { color: var(--primary); }
        .cart-badge-njs { position: absolute; top: -8px; right: -8px; font-size: 0.65rem; padding: 0.25em 0.5em; border-radius: 50px; background-color: #e63946; color: white; border: 2px solid white; line-height: 1; }
        .dropdown-toggle.icon-action::after { display: none; }

        /* CART PAGE */
        .cart-card { background: #fff; border-radius: 16px; border: 1px solid #f0f0f0; overflow: hidden; }
        .cart-item-row { padding: 18px 24px; border-bottom: 1px solid #f8f8f8; transition: background .15s; }
        .cart-item-row:last-child { border-bottom: none; }
        .cart-item-row:hover { background: #fafafa; }

        .qty-btn { width: 34px; height: 34px; border-radius: 50%; border: 1.5px solid #dee2e6; background: #fff; font-size: 1rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s; flex-shrink: 0; }
        .qty-btn:hover { border-color: #2b2d42; background: #2b2d42; color: #fff; }
        .qty-input { width: 46px; text-align: center; border: 1.5px solid #dee2e6; border-radius: 8px; margin: 0 8px; outline: none; font-weight: 600; font-size: .9rem; transition: border-color .2s; }
        .qty-input:focus { border-color: var(--primary); }
        .btn-remove-item { border: none; background: #ffebeb; color: #e63946; border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .btn-remove-item:hover { background: #e63946; color: #fff; }

        .product-thumb { width: 80px; height: 80px; object-fit: contain; border-radius: 12px; background: #f5f5f5; padding: 5px; flex-shrink: 0; border: 1px solid #f0f0f0; }
        .summary-card { background: #fff; border-radius: 16px; border: 1px solid #f0f0f0; padding: 24px; position: sticky; top: 100px; }
        
        .shipping-option-card { border-color: #e9ecef; background: #fff; user-select: none; }
        .shipping-option-card:hover { border-color: #c7d2fe !important; background: #f8f9ff !important; }
        .shipping-option-card input[type=radio] { accent-color: var(--primary); }

        .accordion-button:not(.collapsed) { background-color: #eef2ff; color: var(--primary); }
        .accordion-button:focus { box-shadow: none; }
        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg sticky-top navbar-njs">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand d-flex align-items-center me-0" href="<?= site_url('/') ?>">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo Toko" style="height: 50px; width: auto;">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCenter">
            <i class="fas fa-bars fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarCenter">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center mt-3 mt-lg-0">
                <li class="nav-item"><a class="nav-link nav-link-center" href="<?= site_url('/#produk-section') ?>">Shop</a></li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">New Arrivals</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-center dropdown-toggle" href="#" data-bs-toggle="dropdown">Shop by Categories</a>
                    <ul class="dropdown-menu border-0 shadow-sm text-center text-lg-start">
                        <?php foreach ($categories as $c): ?>
                            <li><a class="dropdown-item" href="<?= site_url('/?aside_category=' . urlencode($c)) ?>"><?= htmlspecialchars($c) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">Customer Service</a></li>
            </ul>

            <div class="d-flex align-items-center justify-content-center justify-content-lg-end gap-4 mt-3 mt-lg-0">
                <div class="d-flex align-items-center d-none d-lg-flex">
                    <img src="https://flagcdn.com/w20/id.png" alt="Indonesia" width="22" class="me-2 rounded-1 border border-light">
                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">IDR</span>
                </div>
                <a href="#" class="icon-action" data-bs-toggle="collapse" data-bs-target="#searchBar"><i class="fas fa-search"></i></a>
                <a href="<?= site_url('wishlist') ?>" class="icon-action"><i class="fas fa-heart"></i></a>
                <a href="<?= site_url('cart') ?>" class="icon-action d-flex align-items-center" style="color: #e63946;">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge-njs fw-bold"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($user): ?>
                    <div class="dropdown">
                        <a href="#" class="icon-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="far fa-user"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><span class="dropdown-item-text fw-bold"><?= htmlspecialchars($user['name']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('riwayat') ?>">Riwayat Order</a></li>
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

<div class="collapse bg-white border-bottom position-absolute w-100" id="searchBar" style="z-index: 1030;">
    <div class="container py-3">
        <form method="get" action="<?= site_url('/') ?>" class="d-flex justify-content-center mx-auto" style="max-width: 600px;">
            <div class="input-group">
                <input type="search" name="q" class="form-control rounded-start-pill bg-light border-0 px-4" placeholder="Ketik nama helm...">
                <button class="btn btn-dark rounded-end-pill px-4" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<main class="flex-grow-1">
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0" style="font-size:.85rem;">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active fw-semibold">Keranjang</li>
        </ol>
    </nav>

    <h4 class="fw-bold mb-4">
        <i class="" style="color:var(--primary);"></i> Keranjang Belanja
        <?php if ($cartCount > 0): ?>
            <span class="badge rounded-pill ms-2 fw-normal" style="background:#f0f0f0; color:#555; font-size:.7rem; vertical-align:middle;">
                <?= $cartCount ?> item
            </span>
        <?php endif; ?>
    </h4>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5 my-4">
            <div style="width:110px;height:110px;border-radius:50%;background:#f5f5f5;margin:0 auto 24px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-shopping-bag fa-3x" style="color:#ddd;"></i>
            </div>
            <h5 class="fw-semibold text-muted mb-2">Keranjangmu masih kosong</h5>
            <p class="text-muted mb-4" style="font-size:.9rem;">Yuk pilih helm favoritmu dan tambahkan ke keranjang!</p>
            <a href="<?= site_url('/') ?>" class="btn btn-dark rounded-pill px-5 py-2">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                    <span class="fw-semibold text-muted" style="font-size:.9rem;">Daftar Produk</span>
                    <button type="button" class="btn btn-sm text-danger fw-medium p-0 text-decoration-none" onclick="clearCart()">Kosongkan Keranjang</button>
                </div>

                <div class="cart-card mb-4 shadow-sm">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item-row d-flex align-items-center flex-wrap flex-sm-nowrap gap-3 gap-sm-4">
                        <img src="<?= base_url(htmlspecialchars($item['img'])) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-thumb">
                        <div class="flex-grow-1" style="min-width:120px;">
                            <div class="text-muted mb-1" style="font-size:.7rem; text-transform:uppercase; letter-spacing:1px;">
                                Varian: <?= htmlspecialchars($item['colour']) ?> - <?= htmlspecialchars($item['size']) ?>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size:.95rem;"><?= htmlspecialchars($item['name']) ?></h6>
                            <div class="fw-semibold" style="color:#e63946; font-size:.9rem;"><?= formatRupiah($item['price']) ?></div>
                            <?php if ($item['stock'] < 5): ?>
                                <small class="text-warning fw-bold d-block mt-1" style="font-size:0.7rem;">Sisa stok: <?= $item['stock'] ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center justify-content-between w-100 w-sm-auto mt-2 mt-sm-0 gap-4">
                            <div class="d-flex align-items-center">
                                <form method="post" action="<?= site_url('cart/action') ?>" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="dec">
                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?>">
                                    <button type="submit" class="qty-btn" title="Kurangi">−</button>
                                </form>
                                <form method="post" action="<?= site_url('cart/action') ?>" class="m-0" id="form-update-<?= md5($item['cart_key']) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?>">
                                    <input type="number" name="qty" value="<?= $item['qty'] ?>" class="qty-input" min="1" 
                                           onchange="document.getElementById('form-update-<?= md5($item['cart_key']) ?>').submit()">
                                </form>
                                <form method="post" action="<?= site_url('cart/action') ?>" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="inc">
                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?>">
                                    <button type="submit" class="qty-btn" title="Tambah">+</button>
                                </form>
                            </div>

                            <form method="post" action="<?= site_url('cart/action') ?>" class="m-0 ms-auto ms-sm-0">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?>">
                                <button type="submit" class="btn-remove-item shadow-sm" title="Hapus" onclick="return confirm('Hapus produk ini dari keranjang?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-3">
                    <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary rounded-pill px-4" style="font-size:.85rem;">
                        <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card shadow-sm">
                    <h6 class="fw-bold mb-4" style="text-transform:uppercase; letter-spacing:.5px; font-size:.85rem;">Ringkasan Pesanan</h6>
                    <div class="d-flex flex-column gap-2 mb-3" style="font-size:.88rem;">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted text-truncate pe-2" style="max-width:180px;">
                                    <?= htmlspecialchars($item['name']) ?> <small>(<?= htmlspecialchars($item['colour']) ?>-<?= htmlspecialchars($item['size']) ?>)</small> &times;<?= $item['qty'] ?>
                                </span>
                                <span class="fw-semibold text-nowrap"><?= formatRupiah($item['subtotal']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr style="border-color:#f0f0f0;">
                    
                    <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                        <span class="text-muted">Jasa Pengiriman</span>
                        <span id="summaryShipping" class="fw-semibold text-muted">Belum dipilih</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size:.88rem;">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span id="summaryOngkir" class="fw-semibold text-danger">—</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2" style="border-top:2px solid #f0f0f0;">
                        <span class="fw-bold text-dark">Total Bayar</span>
                        <h5 class="fw-bold m-0" style="color:var(--primary);" id="summaryTotal"><?= formatRupiah($cartTotal) ?></h5>
                    </div>

                    <button type="button" class="btn w-100 text-white rounded-pill fw-bold mt-4 shadow-sm" style="background:#e63946; padding:12px 0;" onclick="openCheckoutModal()">
                        Lanjut ke Pembayaran <i class="fas fa-chevron-right ms-2 fs-6" style="vertical-align:middle;"></i>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>

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

<div class="modal fade" id="shippingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-primary">Pilih Jasa Pengiriman</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:.82rem;">Wajib pilih sebelum lanjut ke pembayaran</p>
                </div>
                <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-2 pt-3">
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($shippingOptions as $idx => $opt): ?>
                        <label class="shipping-option-card border rounded-3 p-3 d-flex align-items-center gap-3" style="cursor:pointer; transition:all .2s;">
                            <input type="radio" name="pilih_ongkir" value="<?= $opt['code'] ?>" 
                                   data-name="<?= $opt['name'] ?>" data-price="<?= $opt['price'] ?>" data-desc="<?= $opt['desc'] ?>"
                                   class="form-check-input mt-0" style="width:1.2rem; height:1.2rem;" <?= $idx===0?'checked':'' ?>>
                            <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0" style="width:40px;height:40px;background:#eef2ff;">
                                <i class="fas <?= $opt['icon'] ?> text-primary" style="font-size:1.1rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark" style="font-size:.9rem;"><?= $opt['name'] ?></div>
                                <div class="text-muted" style="font-size:.75rem;"><?= $opt['desc'] ?></div>
                            </div>
                            <div class="fw-bold" style="color:#e63946; font-size:.9rem;"><?= formatRupiah($opt['price']) ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" style="padding:10px 0;" onclick="pilihOngkir()">Konfirmasi & Lanjut Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 pt-4 pb-3 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <form method="post" action="<?= site_url('checkout') ?>" id="checkoutForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="shipping_code" id="inputShipCode" value="">
                    <input type="hidden" name="shipping_name" id="inputShipName" value="">
                    <input type="hidden" name="shipping_cost" id="inputShipCost" value="0">

                    <div class="d-flex align-items-center gap-3 p-3 border rounded-3 mb-4" style="background:#fcfcfc;">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:40px;height:40px;background:#4361ee;">
                            <i class="fas fa-truck text-white" style="font-size:.95rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.85rem; color:#2b2d42;" id="modalShipName">—</div>
                            <div class="text-muted" style="font-size:.78rem;" id="modalShipDesc">—</div>
                        </div>
                        <div class="fw-bold" style="color:#e63946; font-size:.9rem;" id="modalShipPrice">—</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size:.75rem;" onclick="gantiPengiriman()">Ganti</button>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Alamat Pengiriman</label>
                            <textarea name="address" class="form-control bg-light" rows="4" placeholder="Alamat lengkap pengiriman..." required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted mb-3">Metode Pembayaran</label>
                            <div class="accordion" id="accordionPayment">
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCOD">
                                            <i class="fas fa-hand-holding-dollar me-2 text-warning fs-5"></i> COD (Bayar di Tempat)
                                        </button>
                                    </h2>
                                    <div id="collapseCOD" class="accordion-collapse collapse show" data-bs-parent="#accordionPayment">
                                        <div class="accordion-body bg-light">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" value="COD" checked>
                                                <label class="form-check-label w-100"><strong>Bayar Tunai ke Kurir</strong><br><small class="text-muted">Siapkan uang pas saat pesanan tiba di tujuan.</small></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTF">
                                            <i class="fas fa-credit-card me-2 text-primary fs-5"></i> Transfer / E-Wallet
                                        </button>
                                    </h2>
                                    <div id="collapseTF" class="accordion-collapse collapse" data-bs-parent="#accordionPayment">
                                        <div class="accordion-body bg-light d-flex flex-column gap-2">
                                            <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="BCA Virtual Account"><label class="form-check-label fw-semibold">BCA Virtual Account</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="Mandiri Virtual Account"><label class="form-check-label fw-semibold">Mandiri Virtual Account</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="QRIS"><label class="form-check-label fw-semibold">QRIS (Semua E-Wallet)</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center px-4 py-3 rounded-bottom-4">
                <div>
                    <div class="text-muted" style="font-size:.8rem;">Total Pembayaran</div>
                    <div class="fw-bold fs-5" style="color:#e63946;" id="modalTotalBayar">—</div>
                </div>
                <button type="button" class="btn text-white rounded-pill px-5 fw-bold" style="background:#e63946;" onclick="submitCheckout()">
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('year').textContent = new Date().getFullYear();

    // Fungsi Clear (Hapus Semua Keranjang)
    function clearCart() {
        Swal.fire({
            title: 'Kosongkan Keranjang?',
            text: "Semua barang akan dihapus dari keranjang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const f = document.createElement('form');
                f.method = 'post';
                f.action = '<?= site_url('cart/action') ?>';
                f.innerHTML = '<?= csrf_field() ?><input type="hidden" name="action" value="clear">';
                document.body.appendChild(f);
                f.submit();
            }
        });
    }

    // Modal Validasi Awal & Lanjut
    function openCheckoutModal() {
        <?php if (!$user): ?>
            Swal.fire({
                title: 'Anda Belum Login',
                text: 'Silakan login terlebih dahulu untuk melanjutkan pembayaran.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4361ee',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Login Sekarang',
                cancelButtonText: 'Nanti Saja'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '<?= site_url('auth/login') ?>';
            });
        <?php else: ?>
            new bootstrap.Modal(document.getElementById('shippingModal')).show();
        <?php endif; ?>
    }

    // Fungsi Konfirmasi di Modal Ongkir
    function pilihOngkir() {
        const sel = document.querySelector('input[name="pilih_ongkir"]:checked');
        if (!sel) {
            Swal.fire('Pilih Ongkir', 'Mohon pilih jasa pengiriman terlebih dahulu.', 'warning');
            return;
        }

        const name = sel.dataset.name;
        const price = parseInt(sel.dataset.price);
        const code = sel.value;
        const desc = sel.dataset.desc;

        // Ubah Teks Tampilan Di Ringkasan Samping Kanan
        document.getElementById('summaryShipping').textContent = name;
        document.getElementById('summaryOngkir').textContent = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('summaryTotal').textContent = 'Rp ' + (<?= $cartTotal ?> + price).toLocaleString('id-ID');

        // Isi Hidden Inputs di Form Checkout Modal Kedua
        document.getElementById('inputShipCode').value = code;
        document.getElementById('inputShipName').value = name;
        document.getElementById('inputShipCost').value = price;

        // Ubah Teks Tampilan Di Dalam Checkout Modal Kedua
        document.getElementById('modalShipName').textContent = name;
        document.getElementById('modalShipDesc').textContent = desc;
        document.getElementById('modalShipPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('modalTotalBayar').textContent = 'Rp ' + (<?= $cartTotal ?> + price).toLocaleString('id-ID');

        // Tutup Modal Ongkir, Buka Modal Checkout
        bootstrap.Modal.getInstance(document.getElementById('shippingModal')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('checkoutModal')).show();
        }, 350);
    }

    // Kembalikan Ke Modal Ongkir
    function gantiPengiriman() {
        bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('shippingModal')).show();
        }, 350);
    }

    // Eksekusi Tombol Bayar Sekarang
    function submitCheckout() {
        const addr = document.querySelector('textarea[name="address"]').value;
        if (!addr.trim()) {
            Swal.fire('Alamat Kosong', 'Mohon isi alamat pengiriman terlebih dahulu.', 'error');
            return;
        }
        Swal.fire({
            title: 'Memproses Pembayaran...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        setTimeout(() => { document.getElementById('checkoutForm').submit(); }, 500);
    }
</script>

<?php if (session()->getFlashdata('msg')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: '<?= session()->getFlashdata("msgType") ?? "warning" ?>',
            title: 'Perhatian!',
            text: '<?= session()->getFlashdata("msg") ?>',
            confirmButtonColor: '#4361ee',
            confirmButtonText: 'Mengerti'
        });
    });
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('checkout_success')): 
    $successData = session()->getFlashdata('checkout_success');
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let method = '<?= $successData['method'] ?>';
        let info = '<?= $successData['info'] ?>';
        let total = '<?= number_format($successData['total'], 0, ',', '.') ?>';
        
        let htmlContent = '';
        
        // Kondisi jika metode pembayaran QRIS / E-Wallet
        if (method === 'QRIS' || method === 'GoPay' || method === 'OVO' || method === 'ShopeePay') {
            htmlContent = `
                <p class="mb-3">
                Silakan scan kode QRIS Anda untuk membayar sebesar
                <strong><br>Rp ${total}</strong>
                </p>
                <div class="text-center my-3">
                    <img src="${info}" alt="QRIS Code" class="img-fluid border p-2 bg-white rounded shadow-sm" style="max-width: 220px;">
                </div>
                <small class="text-muted d-block mt-2"><i class="fas fa-spinner fa-spin me-1"></i> Status pembayaran otomatis diperbarui setelah pembayaran sukses.</small>
            `;
        } 
        // Kondisi jika metode pembayaran Virtual Account Bank
        else if (method.includes('Virtual Account')) {
            htmlContent = `
                <p class="mb-2">Silakan transfer sebesar <strong>Rp ${total}</strong> ke nomor Virtual Account berikut:</p>
                <div class="bg-light p-3 rounded text-center my-3 border">
                    <h4 class="fw-bold text-primary mb-2" id="vaNumber" style="letter-spacing: 1px;">${info}</h4>
                    <button type="button" class="btn btn-sm btn-secondary font-monospace py-1 px-3 rounded-pill" style="font-size: 0.75rem;" onclick="navigator.clipboard.writeText('${info}'); Swal.toast = true; Swal.fire({icon: 'success', title: 'Nomor VA disalin!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});">
                        <i class="far fa-copy me-1"></i> Salin Nomor
                    </button>
                </div>
                <small class="text-muted d-block">Metode Bank: <strong>${method}</strong></small>
            `;
        } 
        // Kondisi jika metode pembayaran Cash On Delivery (COD)
        else if (method === 'COD') {
            htmlContent = `
                <p class="mb-2"> <strong>COD (Bayar di Tempat)</strong></p>
                <div class="alert alert-warning text-start my-3" style="font-size: 0.85rem; line-height: 1.5;">
                    <i class="fas fa-info-circle me-1 text-warning"></i> ${info}
                </div>
            `;
        } 
        // fallback default jika ada metode lain
        else {
            htmlContent = `
                <p>Pesanan Anda berhasil diproses dengan metode <strong>${method}</strong> sebesar <strong>Rp ${total}</strong>.</p>
                <p class="text-muted small">${info}</p>
            `;
        }

        Swal.fire({
            icon: 'success',
            title: 'Checkout Berhasil!',
            html: htmlContent,
            confirmButtonColor: '#4361ee',
            confirmButtonText: 'Selesai & Tutup',
            allowOutsideClick: false
        });
    });
</script>
<?php endif; ?>

</body>
</html>