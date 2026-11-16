<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Manajemen Users</title>
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
        
        .avatar-circle { width: 40px; height: 40px; background-color: #eef2ff; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.1rem; }

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
                <h3 class="fw-bold m-0">Manajemen Pengguna</h3>
                <small class="text-muted">Kelola data hak akses pengguna sistem (Administrator dan Customer).</small>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                <i class="fas fa-user-plus me-2"></i> Tambah User Baru
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
                <input type="text" id="searchUsers" class="form-control border-start-0" placeholder="Cari berdasarkan nama atau email pengguna...">
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="usersTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Profil</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Hak Akses / Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="avatar-circle">
                                    <?= strtoupper(substr(esc($u['name']), 0, 1)) ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($u['name']) ?></div>
                                <?php if ($u['id'] == session()->get('user')['id']): ?>
                                    <span class="badge bg-soft-primary text-primary style-xs" style="background-color: #eef2ff; font-size: 0.7rem;">Anda Saat Ini</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($u['email']) ?></td>
                            <td>
                                <?php if ($u['role_id'] == 1): ?>
                                    <span class="badge bg-danger"><i class="fas fa-user-shield me-1"></i> Administrator</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark"><i class="fas fa-user me-1"></i> Customer</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $u['id'] ?>">
                                        <i class="fas fa-user-edit"></i>
                                    </button>
                                    <a href="<?= site_url('admin/users?delete=' . $u['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait transaksi pengguna ini mungkin akan terpengaruh.')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditUser<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="<?= site_url('admin/users') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-user-edit text-primary me-2"></i> Edit Data Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="<?= esc($u['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Alamat Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= esc($u['email']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Password Baru <span class="text-muted fw-normal">(Kosongkan jika tidak diganti)</span></label>
                                                <input type="password" name="password" class="form-control" placeholder="********">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Hak Akses / Role</label>
                                                <select name="role_id" class="form-select" required>
                                                    <option value="2" <?= $u['role_id'] == 2 ? 'selected' : '' ?>>Customer</option>
                                                    <option value="1" <?= $u['role_id'] == 1 ? 'selected' : '' ?>>Administrator</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit_user" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fs-1 d-block mb-3 opacity-25"></i>
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalAddUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?= site_url('admin/users') ?>">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fas fa-user-plus text-primary me-2"></i> Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Buat password akun..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Hak Akses / Role</label>
                            <select name="role_id" class="form-select" required>
                                <option value="2" selected>Customer</option>
                                <option value="1">Administrator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_user" class="btn btn-primary rounded-pill px-4">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fitur Pencarian Real-Time Client Side untuk Pengguna
        document.getElementById('searchUsers').addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll('#usersTable tbody tr').forEach(function (row) {
                const name = row.querySelector('td:nth-child(2)')?.innerText.toLowerCase() || '';
                const email = row.querySelector('td:nth-child(3)')?.innerText.toLowerCase() || '';
                row.style.display = (name.includes(keyword) || email.includes(keyword)) ? '' : 'none';
            });
        });
    </script>
</body>
</html>