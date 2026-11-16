<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;

class Admin extends BaseController
{
    // Cek apakah user adalah admin
    protected function cekAdmin()
    {
        if (!session()->get('user') || session()->get('user')['role_id'] != 1) {
            return redirect()->to(site_url('auth/login'));
        }
        return null;
    }

    // Helper untuk mengambil hitungan notifikasi verifikasi di sidebar
    private function getNeedVerifCount($db)
    {
        return $db->table('orders')
            ->whereIn('payment_status', ['unpaid', 'pending', 'pending_cod'])
            ->where('shipping_status IS NULL OR shipping_status NOT IN ("diproses", "dikirim", "sampai", "selesai")', null, false)
            ->countAllResults();
    }

    // Halaman Dashboard Utama
    public function index()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();

        // 1. Omset Lunas Hari Ini
        $todaySales = $db->query("
            SELECT SUM(total) FROM orders 
            WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'
        ")->getRowArray();
        $todaySales = $todaySales ? array_values($todaySales)[0] : 0;

        // 2. Omset Bulan Ini
        $monthSales = $db->query("
            SELECT SUM(total) FROM orders 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
            AND payment_status = 'paid'
        ")->getRowArray();
        $monthSales = $monthSales ? array_values($monthSales)[0] : 0;

        // 3. Total Transaksi Hari Ini
        $todayTrx = $db->query("
            SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()
        ")->getRowArray();
        $todayTrx = $todayTrx ? array_values($todayTrx)[0] : 0;

        // 4. Stok Menipis
        $lowStock = $db->query("
            SELECT COUNT(*) FROM products WHERE stock <= 5
        ")->getRowArray();
        $lowStock = $lowStock ? array_values($lowStock)[0] : 0;

        // 5. Butuh Verifikasi
        $needVerif = $this->getNeedVerifCount($db);

        // 6. Data Chart 7 Hari Terakhir
        $labels = [];
        $totals = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $val = $db->query("
                SELECT SUM(total) FROM orders 
                WHERE DATE(created_at) = ? AND payment_status = 'paid'
            ", [$date])->getRowArray();
            $val = $val ? array_values($val)[0] : 0;

            $labels[] = date('d M', strtotime($date));
            $totals[]  = (int) $val;
        }

        // 7. Produk Terlaris
        $topProds = $db->query("
            SELECT p.id, p.name, SUM(oi.qty) as sold 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            JOIN orders o ON oi.order_id = o.id
            WHERE o.payment_status = 'paid' 
            GROUP BY p.id, p.name 
            ORDER BY sold DESC 
            LIMIT 5
        ")->getResultArray();

        // 8. 5 Transaksi Terakhir
        $recentOrders = $db->query("
            SELECT o.*, u.name AS user_name 
            FROM orders o 
            JOIN users u ON u.id = o.user_id
            ORDER BY o.created_at DESC 
            LIMIT 5
        ")->getResultArray();

        return view('admin/dashboard', [
            'todaySales'   => $todaySales ?? 0,
            'monthSales'   => $monthSales ?? 0,
            'todayTrx'     => $todayTrx ?? 0,
            'lowStock'     => $lowStock ?? 0,
            'needVerif'    => $needVerif,
            'labels'       => $labels,
            'totals'       => $totals,
            'topProds'     => $topProds,
            'recentOrders' => $recentOrders,
        ]);
    }

    // Halaman Data Pesanan
    public function orders()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();
        $search = $this->request->getGet('search') ?? '';

        $needVerif = $this->getNeedVerifCount($db);

        $builder = $db->table('orders o')
            ->select('o.*, u.name AS user_name')
            ->join('users u', 'o.user_id = u.id')
            ->orderBy("FIELD(o.shipping_status, 'belum_dikirim', 'diproses', 'dikirim', 'selesai')", "ASC", false)
            ->orderBy('o.created_at', 'DESC');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('u.name', $search)
                    ->orLike('o.id', $search)
                    ->orLike('o.payment_status', $search)
                    ->orLike('o.shipping_status', $search)
                    ->groupEnd();
        }

        $orders = $builder->get()->getResultArray();
        $totalOrders = count($orders);

        return view('admin/orders', [
            'needVerif'   => $needVerif,
            'search'      => $search,
            'orders'      => $orders,
            'totalOrders' => $totalOrders
        ]);
    }

    // Halaman Verifikasi Pembayaran (dari verification.php)
    // Halaman Verifikasi Pembayaran (dari verification.php)
    public function verification()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();

        $needVerif = $this->getNeedVerifCount($db);

        // PERBAIKAN: Tambahkan filter shipping_status
        // Agar pesanan yang sudah disetujui (shipping_status = 'diproses') 
        // langsung menghilang dari tabel verifikasi ini.
        $pendingOrders = $db->table('orders o')
            ->select('o.*, u.name AS user_name')
            ->join('users u', 'o.user_id = u.id')
            ->whereIn('o.payment_status', ['unpaid', 'pending', 'pending_cod'])
            ->where('(o.shipping_status IS NULL OR o.shipping_status NOT IN ("diproses", "dikirim", "sampai", "selesai"))', null, false)
            ->orderBy('o.created_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/verification', [
            'needVerif'     => $needVerif,
            'pendingOrders' => $pendingOrders
        ]);
    }

    // Proses Aksi Terima/Tolak Verifikasi (POST dari verification.php)
    public function processVerification()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();
        $orderId = $this->request->getPost('order_id');
        $action = $this->request->getPost('action');

        if ($orderId && $action) {
            if ($action === 'approve') {
                $orderData = $db->table('orders')->select('payment_method')->where('id', $orderId)->get()->getRowArray();
                $isCOD = stripos($orderData['payment_method'] ?? '', 'cod') !== false;

                if ($isCOD) {
                    $db->table('orders')->where('id', $orderId)->update([
                        'payment_status'  => 'pending_cod',
                        'shipping_status' => 'diproses'
                    ]);
                } else {
                    $db->table('orders')->where('id', $orderId)->update([
                        'payment_status'  => 'paid',
                        'shipping_status' => 'diproses'
                    ]);
                }
                session()->setFlashdata('msg', "Pesanan #$orderId berhasil diverifikasi dan masuk ke antrean proses.");
                session()->setFlashdata('msgType', 'success');
            } elseif ($action === 'reject') {
                $db->table('orders')->where('id', $orderId)->update([
                    'payment_status' => 'cancelled'
                ]);
                session()->setFlashdata('msg', "Pesanan #$orderId telah dibatalkan.");
                session()->setFlashdata('msgType', 'danger');
            }
        }

        return redirect()->to(site_url('admin/verification'));
    }

    // Halaman Detail Order (dari order_detail.php)
    public function orderDetail($id = null)
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        if (!$id) return redirect()->to(site_url('admin/orders'));

        $db = \Config\Database::connect();

        // Penanganan POST Update Payment / Shipping Status
        if ($this->request->getMethod() === 'POST' || $this->request->getPost('update_payment') !== null || $this->request->getPost('update_shipping') !== null) {
            if ($this->request->getPost('payment_status') !== null) {
                $db->table('orders')->where('id', $id)->update(['payment_status' => $this->request->getPost('payment_status')]);
                session()->setFlashdata('msg', 'Status pembayaran berhasil diperbarui!');
                session()->setFlashdata('msgType', 'success');
            }
            if ($this->request->getPost('shipping_status') !== null) {
                $db->table('orders')->where('id', $id)->update(['shipping_status' => $this->request->getPost('shipping_status')]);
                session()->setFlashdata('msg', 'Status pengiriman berhasil diperbarui!');
                session()->setFlashdata('msgType', 'success');
            }
            return redirect()->to(site_url("admin/order_detail/$id"));
        }

        $needVerif = $this->getNeedVerifCount($db);

        $order = $db->table('orders o')
            ->select('o.*, u.name AS user_name, u.email AS user_email, u.address AS user_address')
            ->join('users u', 'u.id = o.user_id')
            ->where('o.id', $id)
            ->get()->getRowArray();

        if (!$order) {
            return redirect()->to(site_url('admin/orders'));
        }

        $orderItems = $db->table('order_items oi')
            ->select('oi.*, p.name AS product_name, p.img AS product_img')
            ->join('products p', 'p.id = oi.product_id')
            ->where('oi.order_id', $id)
            ->get()->getResultArray();

        return view('admin/order_detail', [
            'needVerif'  => $needVerif,
            'order'      => $order,
            'orderItems' => $orderItems
        ]);
    }

    // Pengubah Status Cepat melalui tautan (dari halaman utama orders)
    public function updateShippingStatus($id = null, $status = null)
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();

        // Menggunakan parameter $id dan $status langsung dari URL segment rute
        if ($id && in_array($status, ['diproses', 'dikirim', 'selesai'])) {
            if ($status === 'selesai') {
                $db->table('orders')->where('id', $id)->update([
                    'shipping_status' => 'selesai',
                    'payment_status'  => 'paid'
                ]);
            } else {
                $db->table('orders')->where('id', $id)->update([
                    'shipping_status' => $status
                ]);
            }
            session()->setFlashdata('msg', 'Status pengiriman berhasil diperbarui!');
            session()->setFlashdata('msgType', 'success');
        }

        return redirect()->to(site_url('admin/orders'));
    }

    // Halaman Laporan Penjualan (dari laporan.php)
    public function laporan()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();

        $startDate     = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate       = $this->request->getGet('end_date') ?? date('Y-m-t');
        $paymentMethod = $this->request->getGet('payment_method') ?? '';

        $needVerif = $this->getNeedVerifCount($db);

        $builder = $db->table('orders o')
            ->select('o.*, u.name as customer_name')
            ->join('users u', 'o.user_id = u.id')
            ->where('DATE(o.created_at) >=', $startDate)
            ->where('DATE(o.created_at) <=', $endDate)
            ->where('o.payment_status', 'paid');

        if (!empty($paymentMethod)) {
            $builder->where('o.payment_method', $paymentMethod);
        }

        $reports = $builder->orderBy('o.created_at', 'DESC')->get()->getResultArray();

        $totalRevenue = array_sum(array_column($reports, 'total'));
        $totalOrders  = count($reports);

        $paymentMethodsRaw = $db->table('orders')
            ->select('DISTINCT(payment_method) as method')
            ->where('payment_status', 'paid')
            ->orderBy('payment_method', 'ASC')
            ->get()->getResultArray();
        $paymentMethods = array_column($paymentMethodsRaw, 'method');

        return view('admin/laporan', [
            'needVerif'      => $needVerif,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'paymentMethod'  => $paymentMethod,
            'paymentMethods' => $paymentMethods,
            'reports'        => $reports,
            'totalRevenue'   => $totalRevenue,
            'totalOrders'    => $totalOrders
        ]);
    }

    // Halaman Manajemen Produk & Variasi (dari products.php)
    public function products()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();
        $needVerif = $this->getNeedVerifCount($db);

        // 1. Aksi Tambah Produk
        if ($this->request->getPost('add_product') !== null) {
            $name     = $this->request->getPost('name');
            $category = $this->request->getPost('category');
            $price    = $this->request->getPost('price');
            $desc     = $this->request->getPost('description');
            $colours  = $this->request->getPost('colours') ?? [];
            $sizes    = $this->request->getPost('sizes') ?? [];
            $v_stocks = $this->request->getPost('v_stocks') ?? [];

            $totalStock = array_sum($v_stocks);
            $imgName    = "img/default_helmet.jpg";

            $fileImg = $this->request->getFile('img');
            if ($fileImg && $fileImg->isValid() && !$fileImg->hasMoved()) {
                $newName = time() . '_' . $fileImg->getName();
                if ($fileImg->move(ROOTPATH . 'public/img', $newName)) {
                    $imgName = "img/" . $newName;
                }
            }

            try {
                $db->transBegin();

                $db->table('products')->insert([
                    'name'        => $name,
                    'category'    => $category,
                    'price'       => $price,
                    'stock'       => $totalStock,
                    'description' => $desc,
                    'img'         => $imgName
                ]);
                $productId = $db->insertID();

                if (!empty($colours)) {
                    for ($i = 0; $i < count($colours); $i++) {
                        $db->table('product_variants')->insert([
                            'product_id' => $productId,
                            'colour'     => $colours[$i],
                            'size'       => $sizes[$i],
                            'stock'      => (int)$v_stocks[$i]
                        ]);
                    }
                }

                $db->transCommit();
                session()->setFlashdata('msg', 'Produk berhasil ditambahkan!');
                session()->setFlashdata('msgType', 'success');
            } catch (\Exception $e) {
                $db->transRollback();
                session()->setFlashdata('msg', 'Gagal menambah produk: ' . $e->getMessage());
                session()->setFlashdata('msgType', 'danger');
            }
            return redirect()->to(site_url('admin/products'));
        }

        // 2. Aksi Edit Produk
        if ($this->request->getPost('edit_product') !== null) {
            $id       = $this->request->getPost('id');
            $name     = $this->request->getPost('name');
            $category = $this->request->getPost('category');
            $price    = $this->request->getPost('price');
            $desc     = $this->request->getPost('description');
            $colours  = $this->request->getPost('colours') ?? [];
            $sizes    = $this->request->getPost('sizes') ?? [];
            $v_stocks = $this->request->getPost('v_stocks') ?? [];

            $totalStock = array_sum($v_stocks);

            try {
                $db->transBegin();

                $productUpdate = [
                    'name'        => $name,
                    'category'    => $category,
                    'price'       => $price,
                    'stock'       => $totalStock,
                    'description' => $desc
                ];

                $fileImg = $this->request->getFile('img');
                if ($fileImg && $fileImg->isValid() && !$fileImg->hasMoved()) {
                    $newName = time() . '_' . $fileImg->getName();
                    if ($fileImg->move(ROOTPATH . 'public/img', $newName)) {
                        $productUpdate['img'] = "img/" . $newName;
                    }
                }

                $db->table('products')->where('id', $id)->update($productUpdate);

                // Hapus variasi lama, masukkan variasi baru
                $db->table('product_variants')->where('product_id', $id)->delete();

                if (!empty($colours)) {
                    for ($i = 0; $i < count($colours); $i++) {
                        $db->table('product_variants')->insert([
                            'product_id' => $id,
                            'colour'     => $colours[$i],
                            'size'       => $sizes[$i],
                            'stock'      => (int)$v_stocks[$i]
                        ]);
                    }
                }

                $db->transCommit();
                session()->setFlashdata('msg', 'Produk berhasil diperbarui!');
                session()->setFlashdata('msgType', 'success');
            } catch (\Exception $e) {
                $db->transRollback();
                session()->setFlashdata('msg', 'Gagal memperbarui produk: ' . $e->getMessage());
                session()->setFlashdata('msgType', 'danger');
            }
            return redirect()->to(site_url('admin/products'));
        }

        // 3. Aksi Hapus Produk (via GET ?delete=X)
        $deleteId = $this->request->getGet('delete');
        if ($deleteId) {
            $db->table('products')->where('id', $deleteId)->delete();
            session()->setFlashdata('msg', 'Produk berhasil dihapus!');
            session()->setFlashdata('msgType', 'warning');
            return redirect()->to(site_url('admin/products'));
        }

        // Ambil list semua produk beserta variasinya
        $products = $db->table('products')->orderBy('id', 'DESC')->get()->getResultArray();
        foreach ($products as &$p) {
            $p['variants'] = $db->table('product_variants')->where('product_id', $p['id'])->get()->getResultArray();
        }

        return view('admin/products', [
            'needVerif' => $needVerif,
            'products'  => $products
        ]);
    }

    // Halaman Manajemen Pengguna/User (dari users.php)
    public function users()
    {
        $redirect = $this->cekAdmin();
        if ($redirect) return $redirect;

        $db = \Config\Database::connect();
        $needVerif = $this->getNeedVerifCount($db);

        // 1. Tambah User
        if ($this->request->getPost('add_user') !== null) {
            $name     = $this->request->getPost('name');
            $email    = $this->request->getPost('email');
            $password = password_hash($this->request->getPost('password') ?? '', PASSWORD_DEFAULT);
            $roleId   = $this->request->getPost('role_id');

            try {
                $db->table('users')->insert([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => $password,
                    'role_id'  => $roleId
                ]);
                session()->setFlashdata('msg', 'User berhasil ditambahkan!');
                session()->setFlashdata('msgType', 'success');
            } catch (\Exception $e) {
                session()->setFlashdata('msg', 'Gagal menambah user: Email mungkin sudah terdaftar.');
                session()->setFlashdata('msgType', 'danger');
            }
            return redirect()->to(site_url('admin/users'));
        }

        // 2. Edit User
        if ($this->request->getPost('edit_user') !== null) {
            $id     = $this->request->getPost('id');
            $name   = $this->request->getPost('name');
            $email  = $this->request->getPost('email');
            $roleId = $this->request->getPost('role_id');

            $dataUpdate = ['name' => $name, 'email' => $email, 'role_id' => $roleId];

            if (!empty($this->request->getPost('password'))) {
                $dataUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            $db->table('users')->where('id', $id)->update($dataUpdate);
            session()->setFlashdata('msg', 'Data user diperbarui!');
            session()->setFlashdata('msgType', 'info');
            return redirect()->to(site_url('admin/users'));
        }

        // 3. Hapus User (via GET ?delete=X)
        $deleteId = $this->request->getGet('delete');
        if ($deleteId) {
            if ($deleteId == session()->get('user')['id']) {
                session()->setFlashdata('msg', 'Anda tidak bisa menghapus akun Anda sendiri!');
                session()->setFlashdata('msgType', 'danger');
            } else {
                $db->table('users')->where('id', $deleteId)->delete();
                session()->setFlashdata('msg', 'User berhasil dihapus!');
                session()->setFlashdata('msgType', 'warning');
            }
            return redirect()->to(site_url('admin/users'));
        }

        $users = $db->table('users')->get()->getResultArray();

        return view('admin/users', [
            'needVerif' => $needVerif,
            'users'     => $users
        ]);
    }
}