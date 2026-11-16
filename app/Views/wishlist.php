<?php
// Fungsi helper lokal untuk format rupiah di dalam view
function formatRupiah($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Wishlist – Handika Helmet Store</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { 
            --primary: #4361ee; 
            --secondary: #3f37c9; 
            --danger: #e63946;
        }
        body { 
            font-family: "Outfit", sans-serif; 
            background: #fff; 
            color: #2b2d42; 
        }

        /* --- NAVBAR STYLES --- */
        .navbar-njs { background-color: #ffffff; padding: 15px 0; border-bottom: 1px solid #f1f1f1; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .nav-link-center { color: #000; font-weight: 500; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.5rem 1rem !important; transition: all 0.3s ease; }
        .nav-link-center:hover, .nav-link-center.active { color: var(--danger); }
        .icon-action { color: #000; font-size: 1.25rem; transition: all 0.2s ease; cursor: pointer; text-decoration: none; position: relative; }
        .icon-action:hover { color: var(--danger); }
        .cart-badge-njs { position: absolute; top: -6px; right: -8px; font-size: 0.65rem; padding: 0.3em 0.5em; border-radius: 50px; background-color: var(--danger); color: white; border: 2px solid white; line-height: 1; }
        .dropdown-toggle.icon-action::after { display: none; }

        /* --- PRODUCT CARD --- */
        .product-img-wrap { position: relative; padding-top: 100%; overflow: hidden; border-radius: 12px; background: #f5f5f5; }
        .product-img-wrap img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
        .card:hover .product-img-wrap img { transform: scale(1.05); }
        .btn-remove-wish { position: absolute; top: 8px; right: 8px; z-index: 10; background: rgba(255,255,255,0.85); border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .2s, color .2s; }
        .btn-remove-wish:hover { background: var(--danger); color: #fff; }
        .btn-buy-custom { border: 1px solid var(--danger); color: var(--danger); font-size: 0.85rem; padding: 8px 0; background: transparent; transition: all 0.3s; font-weight: 500; }
        .btn-buy-custom:hover { background-color: var(--danger) !important; color: #fff !important; }

        /* --- MODAL STYLES --- */
        #modal-colours .colour-tab { font-size: 0.88rem; font-weight: 500; color: #111; background: none; border: none; border-bottom: 2px solid transparent; padding: 4px 2px; margin-right: 18px; cursor: pointer; transition: all 0.2s; white-space: nowrap; text-decoration: none; }
        #modal-colours .colour-tab.active-colour { color: #111; border-bottom: 2px solid #111; }
        #modal-colours .colour-tab.disabled-colour { color: #ddd; cursor: not-allowed; text-decoration: line-through; }
        
        #modal-sizes .size-box { min-width: 52px; height: 48px; display: flex; align-items: center; justify-content: center; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem; font-weight: 500; color: #333; background: #fff; cursor: pointer; transition: all 0.18s; padding: 0 10px; }
        #modal-sizes .size-box:hover:not(.disabled-size) { border-color: var(--danger); color: var(--danger); }
        #modal-sizes .size-box.active-size { border-color: var(--danger); background: #fff0f1; color: var(--danger); font-weight: 700; }
        #modal-sizes .size-box.disabled-size { color: #ccc; background: #fafafa; border-color: #eee; cursor: not-allowed; text-decoration: line-through; }
        
        .qty-btn { width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid var(--danger); background: transparent; color: var(--danger); font-size: 1.3rem; line-height: 1; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.18s; padding: 0; }
        .qty-btn:hover { background: var(--danger); color: #fff; }

        /* --- FOOTER STYLES --- */
        .footer-link { color: #bbb; text-decoration: none; transition: color 0.2s; }
        .footer-link:hover { color: #fff; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ========================= NAVBAR START ========================= -->
<nav class="navbar navbar-expand-lg navbar-njs sticky-top">
    <div class="container-fluid px-4 px-lg-5">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center me-0" href="<?= site_url('/') ?>">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo Toko" style="height: 45px; width: auto;">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCenter">
            <i class="fas fa-bars fs-4"></i>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarCenter">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center mt-3 mt-lg-0">
                <li class="nav-item"><a class="nav-link nav-link-center" href="<?= site_url('#produk-section') ?>">Shop</a></li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">New Arrivals</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-center dropdown-toggle" href="#" data-bs-toggle="dropdown">Shop by Categories</a>
                    <ul class="dropdown-menu border-0 shadow-sm text-center text-lg-start">
                        <?php if(!empty($categories)): foreach ($categories as $c): ?>
                            <li><a class="dropdown-item" href="<?= site_url('?aside_category=' . urlencode($c)) ?>"><?= htmlspecialchars($c) ?></a></li>
                        <?php endforeach; endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">Customer Service</a></li>
            </ul>

            <!-- Navbar Actions -->
            <div class="d-flex align-items-center justify-content-center justify-content-lg-end gap-4 mt-3 mt-lg-0">
                <div class="d-flex align-items-center d-none d-lg-flex">
                    <img src="https://flagcdn.com/w20/id.png" alt="Indonesia" width="22" class="me-2 rounded-1 border border-light">
                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">IDR</span>
                </div>

                <a href="#" class="icon-action" data-bs-toggle="collapse" data-bs-target="#searchBar"><i class="fas fa-search"></i></a>
                <a href="<?= site_url('wishlist') ?>" class="icon-action" style="color: var(--danger);"><i class="fas fa-heart"></i></a>
                
                <a href="<?= site_url('cart') ?>" class="icon-action d-flex align-items-center">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if (isset($cartCount) && $cartCount > 0): ?>
                        <span class="cart-badge-njs fw-bold"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <?php if (isset($user) && $user): ?>
                    <div class="dropdown">
                        <a href="#" class="icon-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><span class="dropdown-item-text fw-bold"><?= htmlspecialchars($user['name']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('riwayat') ?>">Riwayat Order</a></li>
                            <?php if (isset($user['role_id']) && $user['role_id'] == 1): ?>
                                <li><a class="dropdown-item" href="<?= site_url('admin/products') ?>">Kelola Produk (Admin)</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="dropdown">
                        <a href="#" class="icon-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-user"></i>
                        </a>
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
<div class="collapse bg-white border-bottom position-absolute w-100" id="searchBar" style="z-index: 1030;">
    <div class="container py-3">
        <form method="get" action="<?= site_url('/') ?>" class="d-flex justify-content-center mx-auto" style="max-width: 600px;">
            <div class="input-group">
                <input type="search" name="q" class="form-control rounded-start-pill bg-light border-0 px-4" placeholder="Ketik nama helm..." required>
                <button class="btn btn-dark rounded-end-pill px-4" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>
<!-- ========================= NAVBAR END ========================= -->


<!-- ========================= MAIN CONTENT START ========================= -->
<main class="flex-grow-1">
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0" style="font-size:.85rem;">
            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active fw-semibold">Wishlist</li>
        </ol>
    </nav>

    <!-- Header -->
    <h4 class="fw-bold mb-4">My Wishlist
        <?php if (!empty($wishlistItems)): ?>
            <span class="badge rounded-pill ms-2 fw-normal" style="background:#f0f0f0; color:#555; font-size:.7rem;">
                <?= count($wishlistItems) ?> item
            </span>
        <?php endif; ?>
    </h4>

    <?php if (empty($wishlistItems)): ?>
        <div class="text-center py-5">
            <div class="mb-4"><i class="far fa-heart" style="font-size: 5rem; color: #eee;"></i></div>
            <h5 class="fw-bold">Wishlist Anda kosong</h5>
            <p class="text-muted">Jelajahi produk kami dan tambahkan produk favorit Anda di sini.</p>
            <a href="<?= site_url('/') ?>" class="btn btn-dark px-4 py-2 mt-3 rounded-pill fw-medium">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($wishlistItems as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 bg-transparent">
                        <div class="product-img-wrap">
                            <a href="<?= site_url('wishlist/remove/' . $p['id']) ?>" class="btn-remove-wish shadow-sm" title="Hapus dari wishlist">
                                <i class="fas fa-times"></i>
                            </a>
                            <?php $isHabis = (($p['stock'] ?? 0) <= 0); ?>
                            <img src="<?= base_url(htmlspecialchars($p['img'] ?? 'img/placeholder.png')) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="<?= $isHabis ? 'opacity-50' : '' ?>">
                            <?php if ($isHabis): ?>
                                <span class="position-absolute top-50 start-50 translate-middle badge bg-dark px-3 py-2" style="font-size:0.8rem; letter-spacing:0.5px;">HABIS</span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body px-0 py-3 text-center d-flex flex-column">
                            <div class="text-muted mb-1" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;"><?= htmlspecialchars($p['category']) ?></div>
                            <h6 class="card-title fw-bold mb-1" style="font-size: 0.95rem; color:#111;"><?= htmlspecialchars($p['name']) ?></h6>
                            <div class="text-muted mb-3 fw-medium" style="font-size: 0.95rem;"><?= formatRupiah($p['price']) ?></div>
                            
                            <div class="mt-auto px-2">
                                <?php if ($p['stock'] > 0): ?>
                                    <button type="button" class="btn w-100 rounded-pill btn-buy-custom"
                                        onclick="openAddToCart(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', '<?= base_url(htmlspecialchars($p['img'] ?? '')) ?>', <?= $p['price'] ?? 0 ?>)">
                                        Beli Sekarang
                                    </button>
                                <?php else: ?>
                                    <button class="btn w-100 rounded-pill" style="border: 1px solid #ddd; color: #999; font-size: 0.85rem; padding: 8px 0; background: #fafafa;" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</main>
<!-- ========================= MAIN CONTENT END ========================= -->


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
<!-- ========================= FOOTER END ========================= -->


<!-- Modal Add To Cart -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content rounded-4 border-0" style="padding: 0; overflow: hidden;">
            <div class="d-flex align-items-center gap-3 px-4 pt-4 pb-3" style="border-bottom:1px solid #f0f0f0;">
                <img id="modal-img" src="" alt="" style="width:72px; height:72px; object-fit:contain; background:#f5f5f5; border-radius:10px; padding:4px; flex-shrink:0;">
                <div class="flex-grow-1">
                    <div class="fw-bold" id="modal-name" style="font-size:1rem; color:#111; line-height:1.3;"></div>
                    <div id="modal-stock-info" class="mt-1" style="font-size:0.8rem; color:#999; display:none;">
                        Stok: <strong id="modal-stock-val" style="color:#e63946;">0</strong> unit
                    </div>
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" style="flex-shrink:0;"></button>
            </div>

            <div style="padding: 20px 24px 24px;">
                <div class="mb-4" id="colour-section">
                    <div class="d-flex flex-wrap align-items-center" id="modal-colours" style="gap:0; border-bottom:1.5px solid #f0f0f0; padding-bottom:0;">
                        <span class="text-muted" style="font-size:0.85rem;">Memuat...</span>
                    </div>
                </div>
                <div class="mb-4" id="size-section" style="display:none;">
                    <div class="fw-semibold mb-3" style="font-size:0.9rem; color:#111;">Ukuran</div>
                    <div class="d-flex flex-wrap gap-2" id="modal-sizes"></div>
                </div>
                <hr style="border-color:#f0f0f0; margin: 0 0 20px;">
                <div class="d-flex align-items-center justify-content-center gap-4 mb-4">
                    <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                    <input type="number" id="modal-qty" min="1" value="1" style="font-size:1.15rem; font-weight:700; width:60px; text-align:center; color:#111; border:none; border-bottom:2px solid #e0e0e0; outline:none; background:transparent; padding:2px 0;" oninput="syncQtyFromInput(this)">
                    <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                </div>

                <form method="post" action="<?= site_url('/') ?>" id="modal-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" id="modal-product-id" value="">
                    <input type="hidden" name="qty" id="modal-qty-input" value="1">
                    <input type="hidden" name="colour" id="modal-colour-input" value="">
                    <input type="hidden" name="size" id="modal-size-input" value="">
                    <button type="submit" class="btn w-100 text-white fw-bold" style="background:#e63946; padding:14px 0; font-size:1rem; border-radius:50px; border:none; letter-spacing:0.3px;">
                        Tambah Ke Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('year').textContent = new Date().getFullYear();

    var isLoggedIn = <?= (isset($user) && $user) ? 'true' : 'false' ?>;
    var modalQty = 1;
    var selectedColour = '';
    var selectedSize = '';
    var currentVariants = {};
    var currentMaxStock = 0;

    function openAddToCart(id, name, img, price) {
        if (!isLoggedIn) { 
            window.location.href = '<?= site_url("auth/login") ?>';
            return; 
        }
        modalQty = 1; selectedColour = ''; selectedSize = '';
        currentVariants = {}; currentMaxStock = 0;

        document.getElementById('modal-product-id').value = id;
        document.getElementById('modal-name').textContent = name;
        document.getElementById('modal-img').src = img;
        document.getElementById('modal-qty').value = 1;
        document.getElementById('modal-qty-input').value = 1;
        document.getElementById('modal-colour-input').value = '';
        document.getElementById('modal-size-input').value = '';
        
        document.getElementById('modal-colours').innerHTML = '<span class="text-muted" style="font-size:0.85rem;">Memuat...</span>';
        document.getElementById('modal-sizes').innerHTML = '';
        document.getElementById('size-section').style.display = 'none';
        document.getElementById('modal-stock-info').style.display = 'none';

        fetch('<?= site_url("get-variants") ?>?product_id=' + id)
            .then(r => r.json())
            .then(data => {
                currentVariants = data; 
                renderColours();
            })
            .catch(() => {
                document.getElementById('modal-colours').innerHTML = '<span class="text-danger" style="font-size:0.85rem;">Gagal memuat variant.</span>';
            });

        new bootstrap.Modal(document.getElementById('addToCartModal')).show();
    }

    function renderColours() {
        var box = document.getElementById('modal-colours');
        var colours = Object.keys(currentVariants);
        if (colours.length === 0) {
            box.innerHTML = '<span class="text-muted" style="font-size:0.85rem;">Tidak ada variant tersedia.</span>';
            return;
        }
        box.innerHTML = '';
        colours.forEach(function(c) {
            var hasStock = Object.values(currentVariants[c]).some(s => s > 0);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'colour-tab' + (!hasStock ? ' disabled-colour' : '');
            btn.textContent = c;
            if (!hasStock) { btn.disabled = true; btn.title = 'Stok habis'; }
            else btn.onclick = function() { selectColour(this, c); };
            box.appendChild(btn);
        });
    }

    function selectColour(el, colour) {
        document.querySelectorAll('.colour-tab').forEach(b => b.classList.remove('active-colour'));
        el.classList.add('active-colour');
        selectedColour = colour; selectedSize = '';
        document.getElementById('modal-colour-input').value = colour;
        document.getElementById('modal-size-input').value = '';
        document.getElementById('modal-stock-info').style.display = 'none';
        currentMaxStock = 0;
        renderSizes(colour);
    }

    function renderSizes(colour) {
        var sizes = currentVariants[colour] || {};
        var box = document.getElementById('modal-sizes');
        box.innerHTML = '';
        var sizeOrder = ['S','M','L','XL','XXL'];
        Object.keys(sizes).forEach(function(s) { if (!sizeOrder.includes(s)) sizeOrder.push(s); });

        sizeOrder.forEach(function(sz) {
            if (!(sz in sizes)) return;
            var stock = sizes[sz];
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'size-box' + (stock <= 0 ? ' disabled-size' : '');
            btn.textContent = sz;
            if (stock <= 0) { btn.disabled = true; btn.title = 'Stok habis'; }
            else btn.onclick = function() { selectSize(this, sz, stock); };
            box.appendChild(btn);
        });
        document.getElementById('size-section').style.display = 'block';
        modalQty = 1;
        document.getElementById('modal-qty').value = 1;
        document.getElementById('modal-qty-input').value = 1;
    }

    function selectSize(el, size, stock) {
        document.querySelectorAll('.size-box').forEach(b => b.classList.remove('active-size'));
        el.classList.add('active-size');
        selectedSize = size; currentMaxStock = stock;
        document.getElementById('modal-size-input').value = size;
        document.getElementById('modal-stock-val').textContent = stock;
        document.getElementById('modal-stock-info').style.display = 'block';
        modalQty = 1;
        document.getElementById('modal-qty').value = 1;
        document.getElementById('modal-qty-input').value = 1;
    }

    function changeQty(delta) {
        var input = document.getElementById('modal-qty');
        var newQty = parseInt(input.value || 1) + delta;
        if (newQty < 1) return;
        if (currentMaxStock > 0 && newQty > currentMaxStock) {
            Swal.fire({ icon:'warning', title:'Stok Terbatas', text:'Stok tersisa hanya ' + currentMaxStock + ' unit.', confirmButtonColor:'#e63946', timer:2000, showConfirmButton:false });
            return;
        }
        modalQty = newQty;
        input.value = modalQty;
        document.getElementById('modal-qty-input').value = modalQty;
    }

    function syncQtyFromInput(el) {
        if (el.value === "") { document.getElementById('modal-qty-input').value = 1; return; }
        var val = parseInt(el.value);
        if (isNaN(val) || val < 1) { val = 1; el.value = 1; }
        if (currentMaxStock > 0 && val > currentMaxStock) {
            Swal.fire({ icon:'warning', title:'Stok Terbatas', text:'Stok tersisa hanya ' + currentMaxStock + ' unit.', confirmButtonColor:'#e63946', timer:2000, showConfirmButton:false });
            val = currentMaxStock; el.value = val;
        }
        modalQty = val;
        document.getElementById('modal-qty-input').value = val;
    }
</script>

<?php if (session()->getFlashdata('msg')): ?>
<script>
    Swal.fire({
        icon: '<?= session()->getFlashdata("msgType") ?? "info" ?>',
        title: 'Informasi',
        text: '<?= session()->getFlashdata("msg") ?>',
        confirmButtonColor: '#e63946',
        timer: 2500,
        showConfirmButton: false
    });
</script>
<?php endif; ?>

</body>
</html>