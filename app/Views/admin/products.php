<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Manajemen Produk</title>
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
        
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .variant-badge { background-color: #f1f3f5; color: #495057; font-size: 0.8rem; padding: 4px 8px; border-radius: 6px; display: inline-block; margin: 2px; }

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
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold m-0">Manajemen Produk</h3>
                <small class="text-muted">Kelola data katalog produk, stok total, gambar, beserta variasi warna & ukuran.</small>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
                <i class="fas fa-plus me-2"></i> Tambah Produk Baru
            </button>
        </div>

        <?php if (session()->getFlashdata('msg')): ?>
            <div class="alert alert-<?= session()->getFlashdata('msgType') ?? 'info' ?> alert-dismissible fade show" role="alert">
                <i class="fas <?= session()->getFlashdata('msgType') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i> <?= session()->getFlashdata('msg') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="content-card mb-4 p-3">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text" id="searchProducts" class="form-control border-start-0" placeholder="Cari berdasarkan nama produk atau kategori...">
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="productsTable">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Total Stok</th>
                            <th>Variasi (Warna - Ukuran [Stok])</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <img src="<?= base_url($p['img']) ?>" alt="Produk" class="product-img border">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($p['name']) ?></div>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;"><?= esc($p['description']) ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= esc($p['category']) ?></span></td>
                            <td class="fw-bold">Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($p['stock'] <= 5): ?>
                                    <span class="badge bg-danger">Sisa <?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $p['stock'] ?> Pcs</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($p['variants'])): ?>
                                    <span class="text-muted small fst-italic">Tidak ada variasi</span>
                                <?php else: ?>
                                    <?php foreach ($p['variants'] as $v): ?>
                                        <div class="variant-badge">
                                            <?= esc($v['colour']) ?> - <?= esc($v['size']) ?> <strong>[<?= $v['stock'] ?>]</strong>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditProduct<?= $p['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= site_url('admin/products?delete=' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini secara permanen?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditProduct<?= $p['id'] ?>"  tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <form method="POST" action="<?= site_url('admin/products') ?>" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i> Edit Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Nama Produk</label>
                                                    <input type="text" name="name" class="form-control" value="<?= esc($p['name']) ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Kategori</label>
                                                    <input type="text" name="category" class="form-control" value="<?= esc($p['category']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Harga (Rp)</label>
                                                    <input type="number" name="price" class="form-control" value="<?= $p['price'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Ganti Foto Produk (Kosongkan jika tidak diubah)</label>
                                                    <input type="file" name="img" class="form-control" accept="image/*">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Deskripsi Produk</label>
                                                    <textarea name="description" class="form-control" rows="3" required><?= esc($p['description']) ?></textarea>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-3">
                                                        <label class="form-label small fw-bold m-0"><i class="fas fa-tags me-1 text-secondary"></i> Pengaturan Variasi & Stok</label>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 text-xs" onclick="addVariantRow('variantContainerEdit<?= $p['id'] ?>')">
                                                            <i class="fas fa-plus me-1"></i> Tambah Baris Variasi
                                                        </button>
                                                    </div>
                                                    
                                                    <div id="variantContainerEdit<?= $p['id'] ?>">
                                                        <?php foreach ($p['variants'] as $v): ?>
                                                        <div class="d-flex align-items-center gap-2 mb-2 variant-row p-2 rounded-3 border bg-light">
                                                            <input type="text" name="colours[]" class="form-control form-control-sm" placeholder="Warna" value="<?= esc($v['colour']) ?>" required>
                                                            <input type="text" name="sizes[]" class="form-control form-control-sm" placeholder="Ukuran" value="<?= esc($v['size']) ?>" required>
                                                            <input type="number" name="v_stocks[]" class="form-control form-control-sm" value="<?= $v['stock'] ?>" min="0" required style="width:70px; flex-shrink:0;">
                                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px; height:28px;" onclick="this.closest('.variant-row').remove()">
                                                                <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                                                            </button>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit_product" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fs-1 d-block mb-3 opacity-25"></i>
                                Belum ada produk terdaftar di database.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalAddProduct" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" action="<?= site_url('admin/products') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fas fa-plus text-primary me-2"></i> Tambah Produk Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Nama Produk</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Helm KYT DJ Maru" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Kategori</label>
                                <input type="text" name="category" class="form-control" placeholder="Contoh: Half Face" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Harga Jual (Rp)</label>
                                <input type="number" name="price" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Foto Produk</label>
                                <input type="file" name="img" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Deskripsi Produk</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Tulis rincian spesifikasi produk di sini..." required></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-3">
                                    <label class="form-label small fw-bold m-0"><i class="fas fa-tags me-1 text-secondary"></i> Pengaturan Variasi & Stok awal</label>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 text-xs" onclick="addVariantRow('variantContainerAdd')">
                                        <i class="fas fa-plus me-1"></i> Tambah Baris Variasi
                                    </button>
                                </div>
                                
                                <div id="variantContainerAdd">
                                    <div class="d-flex align-items-center gap-2 mb-2 variant-row p-2 rounded-3 border bg-light">
                                        <input type="text" name="colours[]" class="form-control form-control-sm" placeholder="Warna" required>
                                        <input type="text" name="sizes[]" class="form-control form-control-sm" placeholder="Ukuran" required>
                                        <input type="number" name="v_stocks[]" class="form-control form-control-sm" value="0" min="0" required style="width:70px; flex-shrink:0;">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px; height:28px;" onclick="this.closest('.variant-row').remove()">
                                            <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_product" class="btn btn-primary rounded-pill px-4">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi Penambah Baris Input Variasi secara Dinamis
        function addVariantRow(containerId) {
            const container = document.getElementById(containerId);
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-2 mb-2 variant-row p-2 rounded-3 border bg-light';
            row.innerHTML = `
                <input type=\"text\" name=\"colours[]\" class=\"form-control form-control-sm\" placeholder=\"Warna\" required>
                <input type=\"text\" name=\"sizes[]\" class=\"form-control form-control-sm\" placeholder=\"Ukuran\" required>
                <input type=\"number\" name=\"v_stocks[]\" class=\"form-control form-control-sm\" value=\"0\" min=\"0\" required style=\"width:70px; flex-shrink:0;\">
                <button type=\"button\" class=\"btn btn-outline-danger btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0\" style=\"width:28px; height:28px;\" onclick=\"this.closest('.variant-row').remove()\"><i class=\"fas fa-times\" style=\"font-size: 0.75rem;\"></i></button>
            `;
            container.appendChild(row);
        }

        // Fitur Pencarian Real-Time Client Side 
        document.getElementById('searchProducts').addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll('#productsTable tbody tr').forEach(function (row) {
                const name = row.querySelector('td:nth-child(2)')?.innerText.toLowerCase() || '';
                const category = row.querySelector('td:nth-child(3)')?.innerText.toLowerCase() || '';
                row.style.display = (name.includes(keyword) || category.includes(keyword)) ? '' : 'none';
            });
        });
    </script>
</body>
</html>