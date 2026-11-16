<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { --primary: #4361ee; --secondary: #3f37c9; --bg-body: #f8f9fa; }
        body { font-family: "Outfit", sans-serif; background-color: var(--bg-body); color: #2b2d42; }
        
        .sidebar { background: white; min-height: 100vh; border-right: 1px solid #eee; position: fixed; width: 250px; top: 0; bottom: 0; z-index: 100; padding-top: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        
        .nav-link { color: #6c757d; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; transition: all 0.2s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background-color: #eef2ff; color: var(--primary); }
        .nav-link i { width: 20px; text-align: center; }
        
        .stat-card { background: white; border: none; border-radius: 12px; padding: 20px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 15px; height: 100%; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .icon-box { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        
        .content-card { background: white; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.03); border: none; padding: 20px; height: 100%; }
        
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
        <a class="nav-link active" href="<?= site_url('admin') ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a class="nav-link" href="<?= site_url('admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Data Pesanan</a>
        <a class="nav-link justify-content-between" href="<?= site_url('admin/verification') ?>">
            <div class="d-flex gap-2 align-items-center"><i class="fas fa-check-double"></i> Verifikasi</div>
            <?php if ($needVerif > 0): ?>
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

    <div class="d-md-none d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0"><i class="fas fa-home me-2"></i>Dashboard</h5>
        <div>
            <a href="<?= site_url('auth/logout') ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-power-off"></i></a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">Dashboard Overview</h3>
            <small class="text-muted">Ringkasan transaksi (Kasir & Web Tokohelm).</small>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <div class="text-muted small fw-bold">OMSET (LUNAS)</div>
                    <h5 class="m-0 fw-bold">Rp <?= number_format($todaySales, 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>

        <?php if ($needVerif > 0): ?>
        <div class="col-md-3">
            <div class="stat-card border border-danger">
                <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="fas fa-bell"></i></div>
                <div>
                    <div class="text-danger small fw-bold">BUTUH VERIFIKASI</div>
                    <h5 class="m-0 fw-bold text-danger"><?= $needVerif ?> Pesanan</h5>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="text-muted small fw-bold">OMSET BULAN INI</div>
                    <h5 class="m-0 fw-bold">Rp <?= number_format($monthSales, 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-info bg-opacity-10 text-info"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <div class="text-muted small fw-bold">TOTAL ORDER (ALL)</div>
                    <h5 class="m-0 fw-bold"><?= $todayTrx ?> Transaksi</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small fw-bold">STOK MENIPIS</div>
                    <h5 class="m-0 fw-bold"><?= $lowStock ?> Item</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="content-card">
                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-chart-line me-2"></i>Tren Penjualan 7 Hari Terakhir</h6>
                <canvas id="salesChart" style="max-height: 300px; width: 100%;"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="content-card">
                <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-crown me-2"></i>Produk Terlaris (All)</h6>
                <ul class="list-group list-group-flush">
                    <?php foreach ($topProds as $idx => $tp): ?>
                    <li class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary rounded-circle" style="width:25px;height:25px;display:flex;align-items:center;justify-content:center;"><?= $idx + 1 ?></span>
                            <span class="fw-bold small text-truncate" style="max-width: 150px;"><?= esc($tp['name']) ?></span>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill"><?= $tp['sold'] ?> terjual</span>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($topProds)): ?>
                        <li class="list-group-item border-0 text-center text-muted small">Belum ada data penjualan.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold m-0"><i class="fas fa-history me-2"></i>5 Transaksi Terakhir (Web & Kasir)</h6>
            <a href="<?= site_url('admin/orders') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID Order</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total Belanja</th>
                        <th>Metode</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ro): ?>
                    <tr>
                        <td class="fw-bold text-primary">#<?= $ro['id'] ?></td>
                        <td><?= esc($ro['user_name']) ?></td>
                        <td><small class="text-muted"><?= date('d M Y H:i', strtotime($ro['created_at'])) ?></small></td>
                        <td class="fw-bold">Rp <?= number_format($ro['total'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($ro['address'] == 'POS TRANSAKSI'): ?>
                                <span class="badge bg-dark">KASIR</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">WEB/ONLINE</span>
                            <?php endif; ?>
                            <span class="badge border text-dark ms-1"><?= esc($ro['payment_method']) ?></span>
                        </td>
                        <td>
                            <?php if ($ro['payment_status'] == 'paid'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success">Lunas</span>
                            <?php elseif (in_array($ro['payment_status'], ['unpaid', 'pending', 'pending_cod'])): ?>
                                <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                            <?php elseif ($ro['payment_status'] == 'cancelled'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger">Batal</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= esc($ro['payment_status']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    const ctx = document.getElementById('salesChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Total Nilai Order (Rp)',
                data: <?= json_encode($totals) ?>,
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4361ee',
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: '#f0f0f0' },
                    ticks: { font: { family: 'Outfit' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Outfit' } }
                }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>