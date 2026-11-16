<?php

namespace App\Controllers;

class Cart extends BaseController
{
    // ============================================================
    // DATA JASA ONGKIR
    // ============================================================
    protected $shippingOptions = [
        ['code' => 'jne_reg',   'name' => 'JNE REG',      'desc' => 'Estimasi 3–5 hari kerja',  'price' => 15000,  'icon' => 'fa-truck'],
        ['code' => 'jne_yes',   'name' => 'JNE YES',       'desc' => 'Estimasi 1–2 hari kerja',  'price' => 28000,  'icon' => 'fa-bolt'],
        ['code' => 'sicepat',   'name' => 'SiCepat BEST', 'desc' => 'Estimasi 2–4 hari kerja',  'price' => 14000,  'icon' => 'fa-truck'],
        ['code' => 'anteraja',  'name' => 'AnterAja',     'desc' => 'Estimasi 2–4 hari kerja',  'price' => 13000,  'icon' => 'fa-truck'],
        ['code' => 'gosend',    'name' => 'GoSend Instant', 'desc' => 'Estimasi < 3 jam (kota)', 'price' => 35000,  'icon' => 'fa-motorcycle'],
        ['code' => 'grab',      'name' => 'GrabExpress',  'desc' => 'Estimasi < 3 jam (kota)', 'price' => 33000,  'icon' => 'fa-motorcycle'],
    ];

    // Halaman Keranjang (GET /cart)
    public function index()
    {
        $db = \Config\Database::connect();
        $session = session();
        $user = $session->get('user');
        
        $cartItems = [];
        $cartCount = 0;
        $cartTotal = 0;
        
        $cart = $session->get('cart') ?? [];

        if (!empty($cart)) {
            $pids = [];
            foreach ($cart as $cartKey => $qty) {
                [$pid] = explode('|', $cartKey);
                $pids[] = (int)$pid;
            }
            $pids = array_unique($pids);
            
            if (!empty($pids)) {
                $query = $db->table('products')->select('id, name, price, img')->whereIn('id', $pids)->get();
                $byId = [];
                foreach ($query->getResultArray() as $r) {
                    $byId[$r['id']] = $r;
                }

                foreach ($cart as $cartKey => $qty) {
                    [$pid, $colour, $size] = explode('|', $cartKey) + [0,'',''];
                    if (!isset($byId[$pid])) continue;
                    
                    $vr = $db->table('product_variants')
                             ->select('stock')
                             ->where(['product_id' => $pid, 'colour' => $colour, 'size' => $size])
                             ->get()->getRowArray();
                    
                    $stockDb = $vr ? (int)$vr['stock'] : 0;

                    $item             = $byId[$pid];
                    $item['cart_key'] = $cartKey;
                    $item['colour']   = $colour;
                    $item['size']     = $size;
                    $item['qty']      = $qty;
                    $item['stock']    = $stockDb;
                    $item['subtotal'] = $item['price'] * $qty;
                    
                    $cartItems[]      = $item;
                    $cartCount       += $qty;
                    $cartTotal       += $item['subtotal'];
                }
            }
        }

        // Kategori Navigasi
        $categoriesData = $db->table('products')->select('category')->distinct()->orderBy('category')->get()->getResultArray();
        $categories = array_column($categoriesData, 'category');

        // Jumlah Wishlist
        $wishCount = 0;
        if ($user) {
            $wishCount = $db->table('wishlist')->where('user_id', $user['id'])->countAllResults();
        }

        return view('cart', [
            'cartItems'       => $cartItems,
            'cartCount'       => $cartCount,
            'cartTotal'       => $cartTotal,
            'categories'      => $categories,
            'wishCount'       => $wishCount,
            'shippingOptions' => $this->shippingOptions,
            'user'            => $user
        ]);
    }

    // Aksi Update / Hapus Keranjang (POST /cart/action)
    public function processAction()
    {
        $db = \Config\Database::connect();
        $session = session();
        $action = $this->request->getPost('action');
        $cartKey = $this->request->getPost('cart_key');
        
        $cart = $session->get('cart') ?? [];

        if ($action === 'inc' && $cartKey && isset($cart[$cartKey])) {
            [$pid, $colour, $size] = explode('|', $cartKey) + [0,'',''];
            $vr = $db->table('product_variants')->select('stock')
                     ->where(['product_id' => $pid, 'colour' => $colour, 'size' => $size])
                     ->get()->getRowArray();
            $sisaStok = $vr ? (int)$vr['stock'] : 0;
            
            if (($cart[$cartKey] + 1) > $sisaStok) {
                $session->setFlashdata('msg', "Maksimal stok tercapai! Hanya tersisa $sisaStok unit.");
                $session->setFlashdata('msgType', 'warning');
            } else {
                $cart[$cartKey]++;
            }
        } elseif ($action === 'dec' && $cartKey && isset($cart[$cartKey])) {
            $cart[$cartKey]--;
            if ($cart[$cartKey] <= 0) unset($cart[$cartKey]);
        } elseif ($action === 'update' && $cartKey) {
            $qty = (int)$this->request->getPost('qty');
            [$pid, $colour, $size] = explode('|', $cartKey) + [0,'',''];
            $vr = $db->table('product_variants')->select('stock')
                     ->where(['product_id' => $pid, 'colour' => $colour, 'size' => $size])
                     ->get()->getRowArray();
                     
            if ($vr) {
                $maxStock = (int)$vr['stock'];
                if ($qty > $maxStock) { 
                    $qty = $maxStock; 
                    $session->setFlashdata('msg', "Gagal mengubah! Stok hanya tersisa $maxStock unit.");
                    $session->setFlashdata('msgType', 'warning');
                }
            }
            if ($qty >= 1) {
                $cart[$cartKey] = $qty;
            } else {
                unset($cart[$cartKey]);
            }
        } elseif ($action === 'remove' && $cartKey) {
            unset($cart[$cartKey]);
        } elseif ($action === 'clear') {
            $cart = [];
        }

        $session->set('cart', $cart);
        return redirect()->to(site_url('cart'));
    }

    // Aksi Checkout Pembayaran (POST /checkout)
    public function checkout()
    {
        $db = \Config\Database::connect();
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            return redirect()->to(site_url('auth/login'));
        }

        $address        = trim($this->request->getPost('address'));
        $payment_method = $this->request->getPost('payment_method') ?? 'COD';
        $shipping_code  = $this->request->getPost('shipping_code');
        $shipping_cost  = (int)$this->request->getPost('shipping_cost');
        $shipping_name  = $this->request->getPost('shipping_name');
        
        $cart = $session->get('cart') ?? [];

        if (count($cart) === 0) {
            $session->setFlashdata('msg', 'Keranjang kosong!');
            return redirect()->to(site_url('cart'));
        } elseif (empty($address)) {
            $session->setFlashdata('msg', 'Harap masukkan alamat pengiriman.');
            return redirect()->to(site_url('cart'));
        } elseif (empty($shipping_code)) {
            $session->setFlashdata('msg', 'Harap pilih jasa pengiriman terlebih dahulu.');
            return redirect()->to(site_url('cart'));
        }

        $pids = [];
        foreach ($cart as $cartKey => $qty) {
            [$pid] = explode('|', $cartKey);
            $pids[] = (int)$pid;
        }
        $pids = array_unique($pids);
        
        $prodById = [];
        if (!empty($pids)) {
            $products = $db->table('products')->select('id, price, name')->whereIn('id', $pids)->get()->getResultArray();
            foreach ($products as $r) {
                $prodById[$r['id']] = $r;
            }
        }

        $total     = 0;
        $stokAman  = true;

        foreach ($cart as $cartKey => $qty) {
            [$pid, $colour, $size] = explode('|', $cartKey) + [0,'',''];
            $vr = $db->table('product_variants')->select('stock')
                     ->where(['product_id' => $pid, 'colour' => $colour, 'size' => $size])
                     ->get()->getRowArray();
                     
            $stockDb = $vr ? (int)$vr['stock'] : 0;

            if ($qty > $stockDb) {
                $pname = $prodById[$pid]['name'] ?? "Produk #$pid";
                $session->setFlashdata('msg', "Stok berubah! $pname ($colour - $size) hanya sisa $stockDb. Harap perbarui keranjang.");
                $session->setFlashdata('msgType', 'warning');
                $stokAman = false;
                break;
            }
            $total += ($prodById[$pid]['price'] ?? 0) * $qty;
        }

        if ($stokAman) {
            $total += $shipping_cost; 
            $payment_info   = "-";
            $payment_status = "unpaid";

            if ($payment_method === 'COD') {
                $payment_info   = "Siapkan uang tunai Rp " . number_format($total, 0, ',', '.') . " saat kurir datang.";
                $payment_status = "pending_cod";
            } elseif (strpos($payment_method, 'Virtual Account') !== false) {
                $bank_code = '8800';
                if (strpos($payment_method, 'BCA') !== false)    $bank_code = '3901';
                if (strpos($payment_method, 'BRI') !== false)    $bank_code = '8881';
                if (strpos($payment_method, 'Mandiri') !== false) $bank_code = '70012';
                $payment_info   = $bank_code . rand(10000000, 99999999);
                $payment_status = 'paid';
            } elseif (in_array($payment_method, ['GoPay','OVO','ShopeePay','QRIS'])) {
                $qr_text        = "Pembayaran Selesai | $payment_method | Total: Rp " . number_format($total, 0, ',', '.');
                $payment_info   = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_text);
                $payment_status = 'paid';
            } elseif ($payment_method === 'Credit Card') {
                $payment_info = "Menunggu otorisasi 3D Secure.";
            }

            $db->transBegin();
            try {
                $orderData = [
                    'user_id' => $user['id'],
                    'total' => $total,
                    'address' => $address,
                    'payment_method' => $payment_method,
                    'payment_info' => $payment_info,
                    'payment_status' => $payment_status,
                    'shipping_service' => $shipping_name,
                    'shipping_cost' => $shipping_cost,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $db->table('orders')->insert($orderData);
                $order_id = $db->insertID();

                foreach ($cart as $cartKey => $qty) {
                    [$pid, $colour, $size] = explode('|', $cartKey) + [0,'',''];
                    $real_id = (int)$pid;
                    $price   = $prodById[$real_id]['price'] ?? 0;
                    
                    $db->table('order_items')->insert([
                        'order_id' => $order_id,
                        'product_id' => $real_id,
                        'price' => $price,
                        'qty' => $qty,
                        'colour' => $colour,
                        'size' => $size
                    ]);

                    // Update variant stock & total product stock
                    $db->query("UPDATE product_variants SET stock = GREATEST(stock - ?, 0) WHERE product_id = ? AND colour = ? AND size = ?", [$qty, $real_id, $colour, $size]);
                    $db->query("UPDATE products SET stock = (SELECT COALESCE(SUM(stock),0) FROM product_variants WHERE product_id = ?) WHERE id = ?", [$real_id, $real_id]);
                }

                $db->transCommit();
                $session->set('cart', []); 

                // ============================================================
                // PENYESUAIAN DISINI: MENGGUNAKAN SESSION FLASHDATA 
                // ============================================================
                $session->setFlashdata('checkout_success', [
                    'method' => $payment_method,
                    'info'   => $payment_info,
                    'total'  => $total
                ]);

                return redirect()->to(site_url('cart'));

            } catch (\Exception $e) {
                $db->transRollback();
                $session->setFlashdata('msg', "Checkout gagal: " . $e->getMessage());
                $session->setFlashdata('msgType', 'error');
                return redirect()->to(site_url('cart'));
            }
        }

        return redirect()->to(site_url('cart'));
    }
}