<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Handika Helmet Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; --bg-body: #ffffff; --danger: #e63946; }
        body { font-family: "Outfit", sans-serif; background-color: var(--bg-body); color: #2b2d42; }
        .accordion-button:not(.collapsed) { background-color:#eef2ff; color:var(--primary); }
        .accordion-button:focus { box-shadow:none; border-color:rgba(0,0,0,.125); }
        .form-check-input:checked { background-color:var(--primary); border-color:var(--primary); }
        
        /* --- NAVBAR STYLES --- */
        .navbar-njs { background-color:#ffffff; padding:15px 0; border-bottom:1px solid #f1f1f1; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .nav-link-center { color:#000; font-weight:500; font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px; padding:0.5rem 1rem !important; transition:all 0.3s ease; }
        .nav-link-center:hover { color:var(--danger); }
        .icon-action { color:#000; font-size:1.25rem; transition:all 0.2s ease; cursor:pointer; text-decoration:none; position:relative; }
        .icon-action:hover { color:var(--danger); }
        .cart-badge-njs { position:absolute; top:-6px; right:-8px; background:var(--danger); color:white; border: 2px solid white; border-radius:50px; font-size:0.65rem; padding: 0.3em 0.5em; line-height: 1; }
        .dropdown-toggle.icon-action::after { display: none; }
        
        /* --- MODAL STYLES --- */
        #modal-colours .colour-tab { font-size:0.88rem; font-weight:500; color:#111; background:none; border:none; border-bottom:2px solid transparent; padding:4px 2px; margin-right:18px; cursor:pointer; transition:all 0.2s; white-space:nowrap; text-decoration:none; }
        #modal-colours .colour-tab.active-colour { color:#111; border-bottom:2px solid #111; }
        #modal-colours .colour-tab.disabled-colour { color:#ddd; cursor:not-allowed; text-decoration:line-through; }
        #modal-sizes .size-box { min-width:52px; height:48px; display:flex; align-items:center; justify-content:center; border:1.5px solid #e0e0e0; border-radius:8px; background:transparent; font-size:0.88rem; font-weight:600; color:#333; cursor:pointer; transition:all 0.18s; }
        #modal-sizes .size-box:hover:not(.disabled-size) { border-color:var(--danger); color:var(--danger); }
        #modal-sizes .size-box.active-size { border-color:var(--danger); background:#fff0f1; color:var(--danger); }
        #modal-sizes .size-box.disabled-size { border-color:#eee; color:#ccc; background: #fafafa; cursor:not-allowed; text-decoration:line-through; }
        .qty-btn { width:36px; height:36px; border-radius:50%; border:1.5px solid var(--danger); background:transparent; color:var(--danger); font-size:1.3rem; line-height:1; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.18s; padding:0; }
        .qty-btn:hover { background:var(--danger); color:#fff; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- ========================= NAVBAR START ========================= -->
<nav class="navbar navbar-expand-lg navbar-njs sticky-top">
    <!-- Diubah ke container-fluid px-4 px-lg-5 -->
    <div class="container-fluid px-4 px-lg-5">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center me-0" href="<?= site_url('/') ?>">
            <img src="<?= base_url('img/logo.png') ?>" alt="Logo Toko" style="height:45px; width:auto;">
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCenter">
            <i class="fas fa-bars fs-4"></i>
        </button>
        
        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarCenter">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center mt-3 mt-lg-0">
                <li class="nav-item"><a class="nav-link nav-link-center" href="#produk-section" onclick="document.getElementById('produk-section').scrollIntoView({behavior:'smooth'}); return false;">Shop</a></li>
                <li class="nav-item"><a class="nav-link nav-link-center" href="#">New Arrivals</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-center dropdown-toggle" href="#" data-bs-toggle="dropdown">Shop by Categories</a>
                    <ul class="dropdown-menu border-0 shadow-sm text-center text-lg-start">
                        <?php if (!empty($categories)): foreach ($categories as $c): ?>
                            <li><a class="dropdown-item" href="<?= site_url('/?aside_category=' . urlencode($c)) ?>"><?= esc($c) ?></a></li>
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
                            <li><span class="dropdown-item-text fw-bold"><?= esc($user['name']) ?></span></li>
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

<!-- Search Bar Overlay -->
<div class="collapse bg-white border-bottom position-absolute w-100" id="searchBar" style="z-index:1030;">
    <div class="container py-3">
        <form method="get" action="<?= site_url('/') ?>" class="d-flex justify-content-center mx-auto" style="max-width:600px;">
            <div class="input-group">
                <input type="search" name="q" class="form-control rounded-start-pill bg-light border-0 px-4" placeholder="Ketik nama helm..." value="<?= esc($search ?? '') ?>">
                <button class="btn btn-dark rounded-end-pill px-4" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>
<!-- ========================= NAVBAR END ========================= -->


<!-- ========================= MAIN CONTENT START ========================= -->
<main class="flex-grow-1">

    <!-- Hero Carousel -->
    <div class="container-fluid px-0">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="4000">
                    <img src="<?= base_url('img/FromLD.png') ?>" class="d-block w-100" alt="Banner 1"
                         style="height:calc(100vh - 81px); min-height:500px; object-fit:cover; object-position:center; cursor:pointer;"
                         onclick="openAddToCart(17, 'NJS NEOZ XV.2 Musashi', '<?= base_url('img/FromLD.png') ?>', 750000)">
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="<?= base_url('img/New1.png') ?>" class="d-block w-100" alt="Banner 2"
                         style="height:calc(100vh - 81px); min-height:500px; object-fit:cover; object-position:center; cursor:pointer;"
                         onclick="openAddToCart(15, 'NJS NEOZ XV.2 Stellar', '<?= base_url('img/New1.png') ?>', 650000)">
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="<?= base_url('img/New2.png') ?>" class="d-block w-100" alt="Banner 3"
                         style="height:calc(100vh - 81px); min-height:500px; object-fit:cover; object-position:center; cursor:pointer;"
                         onclick="openAddToCart(16, 'NJS Freedom Skyline', '<?= base_url('img/New2.png') ?>', 550000)">
                </div>
            </div>
        </div>
    </div>

    <!-- Product Section -->
    <div class="container mb-5 mt-5">
        <h5 class="text-center fw-bold mb-4" style="letter-spacing:0.5px; text-transform:uppercase;">Shop</h5>
        <div id="produk-section" class="row g-4 justify-content-center">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center text-muted">Belum ada produk.</div>
            <?php else: foreach ($products as $p):
                $isWishlisted = isset($wishlisted[$p['id']]);
                $isHabis      = (($p['stock'] ?? 0) <= 0);
            ?>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 bg-transparent">
                    <div class="position-relative" style="padding-top:100%; overflow:hidden; border-radius:12px; background:#f5f5f5;">
                        <img src="<?= esc($p['img'] ?? base_url('img/placeholder.png')) ?>" alt="<?= esc($p['name']) ?>"
                             class="position-absolute top-0 start-0 w-100 h-100"
                             style="object-fit:cover; <?= $isHabis ? 'opacity:0.45;' : '' ?>">
                        <?php if ($isHabis): ?>
                            <div class="position-absolute top-0 start-0 m-2 px-2 py-1 fw-bold text-white"
                                 style="background:#111; font-size:0.7rem; letter-spacing:0.5px; border-radius:4px; z-index:5;">STOK HABIS</div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?= site_url('wishlist') ?>" class="position-absolute bottom-0 end-0 p-2" style="z-index:10; margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="redirect" value="/">
                            <button type="submit" class="border-0 bg-transparent p-0" style="cursor:pointer; line-height:1;">
                                <i class="<?= $isWishlisted ? 'fas' : 'far' ?> fa-heart fs-5 <?= $isWishlisted ? 'text-danger' : 'text-white' ?>" style="filter:drop-shadow(0px 1px 2px rgba(0,0,0,0.4));"></i>
                            </button>
                        </form>
                    </div>
                    <div class="card-body px-0 pt-3 pb-0 text-start d-flex flex-column">
                        <h6 class="card-title text-truncate mb-1" style="font-size:0.85rem; font-weight:500; color:#111;"><?= esc($p['name']) ?></h6>
                        <p class="card-text mb-3" style="color:#e63946; font-size:0.9rem; font-weight:500;">Rp <?= number_format($p['price'] ?? 0, 0, ',', '.') ?></p>
                        <div class="mt-auto">
                            <?php if (!$isHabis): ?>
                                <button type="button" class="btn w-100 rounded-pill"
                                    style="border:1px solid #e63946; color:#e63946; font-size:0.85rem; padding:6px 0; background:transparent; transition:all 0.3s;"
                                    onmouseover="this.style.backgroundColor='#e63946'; this.style.color='#fff';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#e63946';"
                                    onclick="openAddToCart(<?= $p['id'] ?>, '<?= esc(addslashes($p['name'])) ?>', '<?= esc($p['img'] ?? '') ?>', <?= $p['price'] ?>)">Buy</button>
                            <?php else: ?>
                                <button type="button" class="btn w-100 rounded-pill"
                                    style="border:1px solid #bbb; color:#555; font-size:0.85rem; padding:6px 0; background:transparent;"
                                    onclick="beritahuSaya(<?= $p['id'] ?>, '<?= esc(addslashes($p['name'])) ?>')">Beritahu Saya</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Feature Section -->
    <div class="py-5" style="background-color:#f5f5f5;">
        <div class="container px-4 px-lg-5">
            <div class="row text-center align-items-center justify-content-center">
                <div class="col-6 col-md-3 mb-5 mb-md-0 d-flex flex-column justify-content-center align-items-center">
                    <img src="<?= base_url('img/1.webp') ?>" alt="Worldwide Shipping" style="height:120px; width:auto; object-fit:contain;" class="mb-3">
                </div>
                <div class="col-6 col-md-3 mb-5 mb-md-0 d-flex flex-column justify-content-center align-items-center">
                    <img src="<?= base_url('img/2.webp') ?>" alt="SNI" style="height:120px; width:auto; object-fit:contain;" class="mb-2">
                </div>
                <div class="col-6 col-md-3 mb-5 mb-md-0 d-flex flex-column justify-content-center align-items-center">
                    <img src="<?= base_url('img/3.webp') ?>" alt="Maintenance" style="height:120px; width:auto; object-fit:contain;" class="mb-2">
                </div>
                <div class="col-6 col-md-3 d-flex flex-column justify-content-center align-items-center">
                    <img src="<?= base_url('img/4.webp') ?>" alt="Return & Exchange" style="height:120px; width:auto; object-fit:contain;" class="mb-3">
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="authPromptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
            <div class="modal-content rounded-4 border-0 text-center" style="padding:32px 24px;">
                <div class="mb-3"><i class="far fa-user-circle" style="font-size:3.5rem; color:#e63946;"></i></div>
                <h5 class="fw-bold mb-1">Masuk Terlebih Dahulu</h5>
                <p class="text-muted mb-4" style="font-size:0.9rem;">Kamu perlu login atau daftar untuk melanjutkan tindakan ini.</p>
                <div class="d-grid gap-2">
                    <a href="<?= site_url('auth/login') ?>" class="btn rounded-pill text-white fw-semibold" style="background:#e63946; padding:10px 0;">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="<?= site_url('auth/register') ?>" class="btn btn-outline-secondary rounded-pill fw-semibold" style="padding:10px 0;">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content rounded-4 border-0" style="padding:0; overflow:hidden;">
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
                <div style="padding:20px 24px 24px;">
                    <div class="mb-4" id="colour-section">
                        <div class="fw-semibold mb-3" style="font-size:0.9rem; color:#111;">Pilih Warna</div>
                        <div class="d-flex flex-wrap align-items-center" id="modal-colours" style="border-bottom:1.5px solid #f0f0f0; padding-bottom:0;">
                            <span class="text-muted" style="font-size:0.85rem;">Memuat...</span>
                        </div>
                    </div>
                    <div class="mb-4" id="size-section" style="display:none;">
                        <div class="fw-semibold mb-3" style="font-size:0.9rem; color:#111;">Ukuran</div>
                        <div class="d-flex flex-wrap gap-2" id="modal-sizes"></div>
                    </div>
                    <hr style="border-color:#f0f0f0; margin:0 0 20px;">
                    <div class="d-flex align-items-center justify-content-center gap-4 mb-4">
                        <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                        <input type="number" id="modal-qty" min="1" value="1"
                            style="font-size:1.15rem; font-weight:700; width:60px; text-align:center; color:#111; border:none; border-bottom:2px solid #e0e0e0; outline:none; background:transparent; padding:2px 0;"
                            oninput="syncQtyFromInput(this)"
                            onblur="if(this.value===''){this.value=1;syncQtyFromInput(this);}">
                        <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                    </div>
                    
                    <form method="post" action="<?= site_url('/') ?>" id="modal-form" onsubmit="return validateCartForm()">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" id="modal-product-id" value="">
                        <input type="hidden" name="qty" id="modal-qty-input" value="1">
                        <input type="hidden" name="colour" id="modal-colour-input" value="">
                        <input type="hidden" name="size" id="modal-size-input" value="">
                        <button type="submit" class="btn w-100 text-white fw-bold"
                            style="background:#e63946; padding:14px 0; font-size:1rem; border-radius:50px; border:none; letter-spacing:0.3px;">
                            Tambah Ke Keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>
<!-- ========================= MAIN CONTENT END ========================= -->

<!-- ========================= FOOTER START ========================= -->
<footer class="bg-dark text-white pt-5 pb-4 mt-auto">
    <!-- Menggunakan px-4 px-lg-5 yang sejajar dengan Source 1 -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('year').textContent = new Date().getFullYear();

    document.addEventListener('DOMContentLoaded', function () {
        var heroCarousel = document.getElementById('heroCarousel');
        if (heroCarousel) {
            new bootstrap.Carousel(heroCarousel, { interval: 4000, ride: 'carousel', wrap: true }).cycle();
        }
    });

    // === MODAL ADD TO CART ===
    var isLoggedIn = <?= $user ? 'true' : 'false' ?>;
    var modalQty = 1, selectedColour = '', selectedSize = '', currentVariants = {}, currentMaxStock = 0;

    function openAddToCart(id, name, img, price) {
        if (!isLoggedIn) {
            document.getElementById('modal-product-id').value = id;
            new bootstrap.Modal(document.getElementById('authPromptModal')).show();
            return;
        }
        modalQty = 1; selectedColour = ''; selectedSize = ''; currentVariants = {}; currentMaxStock = 0;
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

        fetch('<?= site_url('get-variants') ?>?product_id=' + id)
            .then(r => r.json())
            .then(data => { currentVariants = data; renderColours(); })
            .catch(() => { document.getElementById('modal-colours').innerHTML = '<span class="text-danger" style="font-size:0.85rem;">Gagal memuat variant.</span>'; });

        new bootstrap.Modal(document.getElementById('addToCartModal')).show();
    }

    function validateCartForm() {
        if (document.getElementById('modal-colour-input').value === '') {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Silakan pilih Warna terlebih dahulu!', confirmButtonColor: '#e63946' });
            return false;
        }
        if (document.getElementById('size-section').style.display === 'block' && document.getElementById('modal-size-input').value === '') {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Silakan pilih Ukuran terlebih dahulu!', confirmButtonColor: '#e63946' });
            return false;
        }
        return true;
    }

    function beritahuSaya(id, name) {
        if (!isLoggedIn) {
            new bootstrap.Modal(document.getElementById('authPromptModal')).show();
            return;
        }
        Swal.fire({ icon:'info', title:'Stok Habis', html:'<p>Produk <b>' + name + '</b> sedang tidak tersedia.</p>', confirmButtonColor:'#e63946', confirmButtonText:'<i class="fas fa-heart me-1"></i> Tambah ke Wishlist', showCancelButton:true, cancelButtonText:'Tutup', cancelButtonColor:'#aaa' })
        .then((result) => {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'post'; form.action = '<?= site_url('wishlist') ?>';
                form.innerHTML = '<input name="product_id" value="' + id + '"><input name="redirect" value="/">';
                document.body.appendChild(form); form.submit();
            }
        });
    }

    function renderColours() {
        var box = document.getElementById('modal-colours');
        var colours = Object.keys(currentVariants);
        if (colours.length === 0) { box.innerHTML = '<span class="text-muted" style="font-size:0.85rem;">Tidak ada variant tersedia.</span>'; return; }
        box.innerHTML = '';
        colours.forEach(function(c) {
            var hasStock = Object.values(currentVariants[c]).some(s => s > 0);
            var btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'colour-tab' + (!hasStock ? ' disabled-colour' : '');
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
        var box = document.getElementById('modal-sizes'); box.innerHTML = '';
        var sizeOrder = ['S','M','L','XL','XXL'];
        Object.keys(sizes).forEach(function(s) { if (!sizeOrder.includes(s)) sizeOrder.push(s); });
        sizeOrder.forEach(function(sz) {
            if (!(sz in sizes)) return;
            var stock = sizes[sz];
            var btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'size-box' + (stock <= 0 ? ' disabled-size' : '');
            btn.textContent = sz;
            if (stock <= 0) { btn.disabled = true; btn.title = 'Stok habis'; }
            else btn.onclick = function() { selectSize(this, sz, stock); };
            box.appendChild(btn);
        });
        document.getElementById('size-section').style.display = 'block';
        modalQty = 1; document.getElementById('modal-qty').value = 1; document.getElementById('modal-qty-input').value = 1;
    }

    function selectSize(el, size, stock) {
        document.querySelectorAll('.size-box').forEach(b => b.classList.remove('active-size'));
        el.classList.add('active-size');
        selectedSize = size; currentMaxStock = stock;
        document.getElementById('modal-size-input').value = size;
        document.getElementById('modal-stock-val').textContent = stock;
        document.getElementById('modal-stock-info').style.display = 'block';
        modalQty = 1; document.getElementById('modal-qty').value = 1; document.getElementById('modal-qty-input').value = 1;
    }

    function changeQty(delta) {
        var input = document.getElementById('modal-qty');
        var newQty = parseInt(input.value || 1) + delta;
        if (newQty < 1) return;
        if (currentMaxStock > 0 && newQty > currentMaxStock) {
            Swal.fire({ icon:'warning', title:'Stok Terbatas', text:'Stok tersisa hanya ' + currentMaxStock + ' unit.', confirmButtonColor:'#e63946', timer:2000, showConfirmButton:false });
            return;
        }
        modalQty = newQty; input.value = modalQty; document.getElementById('modal-qty-input').value = modalQty;
    }

    function syncQtyFromInput(el) {
        if (el.value === "") { document.getElementById('modal-qty-input').value = 1; return; }
        var val = parseInt(el.value);
        if (isNaN(val) || val < 1) { val = 1; el.value = 1; }
        if (currentMaxStock > 0 && val > currentMaxStock) {
            Swal.fire({ icon:'warning', title:'Stok Terbatas', text:'Stok tersisa hanya ' + currentMaxStock + ' unit.', confirmButtonColor:'#e63946', timer:2000, showConfirmButton:false });
            val = currentMaxStock; el.value = val;
        }
        modalQty = val; document.getElementById('modal-qty-input').value = val;
    }
</script>

<?php if (!empty($msg)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon:'warning', title:'Perhatian!', text:'<?= esc($msg) ?>', confirmButtonColor:'#4361ee', confirmButtonText:'Oke, Mengerti' });
    });
</script>
<?php endif; ?>

<?php if (request()->getGet('status') === 'success'): ?>
<script>
    const method = "<?= esc(request()->getGet('method') ?? '') ?>";
    const info   = "<?= esc(urldecode(request()->getGet('info') ?? '')) ?>";
    let swalConfig = { title:'Pesanan Berhasil!', confirmButtonColor:'#4361ee', confirmButtonText:'Selesai', allowOutsideClick:false };
    if (info.startsWith('http')) {
        swalConfig.text = 'Scan QR Code dibawah ini untuk pembayaran:';
        swalConfig.imageUrl = info; swalConfig.imageWidth = 250; swalConfig.imageHeight = 250; swalConfig.imageAlt = 'QR Code Pembayaran';
    } else {
        swalConfig.icon = 'success';
        swalConfig.html = '<p>Metode: <b>' + method + '</b></p><div class="alert alert-light mt-2 border">' + info + '</div>';
    }
    Swal.fire(swalConfig).then((result) => { if (result.isConfirmed) window.location.href = '<?= site_url('riwayat') ?>'; });
</script>
<?php endif; ?>

</body>
</html>