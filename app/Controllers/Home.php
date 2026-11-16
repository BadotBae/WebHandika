<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Inisialisasi cart di session jika belum ada
        if (!session()->has('cart')) {
            session()->set('cart', []);
        }

        $db     = \Config\Database::connect();
        $action = $this->request->getPost('action');
        $msg    = '';

        // -------------------------------------------------------
        // LOGIKA CART & CHECKOUT
        // -------------------------------------------------------
        if ($action) {

            // 1. ADD TO CART
            if ($action === 'add' && $this->request->getPost('product_id')) {
                $pid     = (int) $this->request->getPost('product_id');
                $colour  = trim($this->request->getPost('colour') ?? '');
                $size    = trim($this->request->getPost('size') ?? '');
                $qtyAdd  = max(1, (int) ($this->request->getPost('qty') ?? 1));
                $cartKey = $pid . '|' . $colour . '|' . $size;

                $variant = $db->query(
                    "SELECT stock FROM product_variants WHERE product_id = ? AND colour = ? AND size = ?",
                    [$pid, $colour, $size]
                )->getRowArray();

                if (!$variant) {
                    $msg = "Silakan pilih warna dan ukuran yang tersedia.";
                } else {
                    $cart       = session()->get('cart');
                    $currentQty = $cart[$cartKey] ?? 0;
                    $sisaStok   = (int) $variant['stock'];
                    if (($currentQty + $qtyAdd) > $sisaStok) {
                        $msg = "Gagal menambah! Stok $colour - $size hanya tersisa $sisaStok unit.";
                    } else {
                        $cart[$cartKey] = $currentQty + $qtyAdd;
                        session()->set('cart', $cart);
                    }
                }

            // 2. INCREMENT
            } elseif ($action === 'inc' && $this->request->getPost('product_id')) {
                $cartKey = $this->request->getPost('cart_key') ?? '';
                $cart    = session()->get('cart');
                if ($cartKey && isset($cart[$cartKey])) {
                    [$pid, $colour, $size] = explode('|', $cartKey) + [0, '', ''];
                    $variant = $db->query(
                        "SELECT stock FROM product_variants WHERE product_id = ? AND colour = ? AND size = ?",
                        [$pid, $colour, $size]
                    )->getRowArray();
                    $sisaStok   = $variant ? (int) $variant['stock'] : 0;
                    $currentQty = $cart[$cartKey];
                    if (($currentQty + 1) > $sisaStok) {
                        $msg = "Maksimal stok tercapai! Hanya tersisa $sisaStok unit.";
                    } else {
                        $cart[$cartKey]++;
                        session()->set('cart', $cart);
                    }
                }

            // 3. DECREMENT
            } elseif ($action === 'dec' && $this->request->getPost('product_id')) {
                $cartKey = $this->request->getPost('cart_key') ?? '';
                $cart    = session()->get('cart');
                if ($cartKey && isset($cart[$cartKey])) {
                    $cart[$cartKey]--;
                    if ($cart[$cartKey] <= 0) unset($cart[$cartKey]);
                    session()->set('cart', $cart);
                }

            // 4. UPDATE QTY
            } elseif ($action === 'update' && $this->request->getPost('product_id')) {
                $cartKey = $this->request->getPost('cart_key') ?? '';
                $qty     = (int) $this->request->getPost('qty');
                $cart    = session()->get('cart');
                if ($cartKey) {
                    [$pid, $colour, $size] = explode('|', $cartKey) + [0, '', ''];
                    $variant = $db->query(
                        "SELECT stock FROM product_variants WHERE product_id = ? AND colour = ? AND size = ?",
                        [$pid, $colour, $size]
                    )->getRowArray();
                    if ($variant) {
                        $maxStock = (int) $variant['stock'];
                        if ($qty > $maxStock) { $qty = $maxStock; $msg = "Gagal menambah! Stok hanya tersisa $maxStock unit."; }
                    }
                    if ($qty >= 1) $cart[$cartKey] = $qty;
                    else unset($cart[$cartKey]);
                    session()->set('cart', $cart);
                }

            // 5. REMOVE ITEM
            } elseif ($action === 'remove' && $this->request->getPost('product_id')) {
                $cartKey = $this->request->getPost('cart_key') ?? '';
                $cart    = session()->get('cart');
                if ($cartKey) { unset($cart[$cartKey]); session()->set('cart', $cart); }

            // 6. CLEAR CART
            } elseif ($action === 'clear') {
                session()->set('cart', []);

            // 7. CHECKOUT
            } elseif ($action === 'checkout') {
                if (!session()->get('user')) {
                    return redirect()->to(site_url('auth/login'));
                }

                $address        = trim($this->request->getPost('address') ?? '');
                $payment_method = $this->request->getPost('payment_method') ?? 'COD';
                $cart           = session()->get('cart');

                if (count($cart) === 0) {
                    $msg = "Keranjang kosong!";
                } elseif (empty($address)) {
                    $msg = "Harap masukkan alamat lengkap tujuan pengiriman.";
                } else {
                    // Ambil harga produk
                    $pids = array_unique(array_map(fn($k) => (int)explode('|', $k)[0], array_keys($cart)));
                    $placeholders = implode(',', array_fill(0, count($pids), '?'));
                    $prodById = [];
                    foreach ($db->query("SELECT id, price, name FROM products WHERE id IN ($placeholders)", $pids)->getResultArray() as $r) {
                        $prodById[$r['id']] = $r;
                    }

                    $total     = 0;
                    $stokAman  = true;

                    foreach ($cart as $cartKey => $qty) {
                        [$pid, $colour, $size] = explode('|', $cartKey) + [0, '', ''];
                        $vr       = $db->query("SELECT stock FROM product_variants WHERE product_id = ? AND colour = ? AND size = ?", [$pid, $colour, $size])->getRowArray();
                        $stockDb  = $vr ? (int) $vr['stock'] : 0;
                        if ($qty > $stockDb) {
                            $pname = $prodById[$pid]['name'] ?? "Produk #$pid";
                            $msg   = "Stok berubah! $pname ($colour - $size) hanya sisa $stockDb. Harap update keranjang.";
                            $stokAman = false;
                            break;
                        }
                        $total += ($prodById[$pid]['price'] ?? 0) * $qty;
                    }

                    if ($stokAman) {
                        $payment_info   = "-";
                        $payment_status = "unpaid";

                        if ($payment_method == 'COD') {
                            $payment_info   = "Siapkan uang tunai Rp " . number_format($total, 0, ',', '.') . " saat kurir datang.";
                            $payment_status = "pending_cod";
                        } elseif (strpos($payment_method, 'Virtual Account') !== false) {
                            $bank_code = '8800';
                            if (strpos($payment_method, 'BCA') !== false)     $bank_code = '3901';
                            if (strpos($payment_method, 'BRI') !== false)     $bank_code = '8881';
                            if (strpos($payment_method, 'Mandiri') !== false) $bank_code = '70012';
                            $payment_info = $bank_code . rand(10000000, 99999999);
                        } elseif (
                            strpos($payment_method, 'GoPay') !== false ||
                            strpos($payment_method, 'OVO') !== false ||
                            strpos($payment_method, 'ShopeePay') !== false ||
                            strpos($payment_method, 'QRIS') !== false
                        ) {
                            $qr_text        = "Pembayaran Selesai | $payment_method | Total: Rp " . number_format($total, 0, ',', '.');
                            $payment_info   = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qr_text);
                            $payment_status = 'paid';
                        } elseif ($payment_method == 'Credit Card') {
                            $payment_info = "Menunggu otorisasi 3D Secure.";
                        }

                        // Simpan ke DB
                        $db->transStart();
                        try {
                            $userId = session()->get('user')['id'];
                            $db->query(
                                "INSERT INTO orders (user_id, total, address, payment_method, payment_info, payment_status) VALUES (?, ?, ?, ?, ?, ?)",
                                [$userId, $total, $address, $payment_method, $payment_info, $payment_status]
                            );
                            $order_id = $db->insertID();

                            foreach ($cart as $cartKey => $qty) {
                                [$pid, $colour, $size] = explode('|', $cartKey) + [0, '', ''];
                                $real_id = (int) $pid;
                                $price   = $prodById[$real_id]['price'] ?? 0;
                                $db->query("INSERT INTO order_items (order_id, product_id, price, qty, colour, size) VALUES (?, ?, ?, ?, ?, ?)", [$order_id, $real_id, $price, $qty, $colour, $size]);
                                $db->query("UPDATE product_variants SET stock = GREATEST(stock - ?, 0) WHERE product_id = ? AND colour = ? AND size = ?", [$qty, $real_id, $colour, $size]);
                                $db->query("UPDATE products SET stock = (SELECT COALESCE(SUM(stock),0) FROM product_variants WHERE product_id = ?) WHERE id = ?", [$real_id, $real_id]);
                            }

                            $db->transComplete();
                            session()->set('cart', []);

                            return redirect()->to(site_url('/?status=success&method=' . urlencode($payment_method) . '&info=' . urlencode($payment_info)));
                        } catch (\Exception $e) {
                            $db->transRollback();
                            $msg = "Checkout gagal: " . $e->getMessage();
                        }
                    }
                }
            }
        }

        // -------------------------------------------------------
        // FILTER & DATA PRODUK
        // -------------------------------------------------------
        $search        = trim($this->request->getGet('q') ?? '');
        $asideCategory = $this->request->getGet('aside_category') ?? 'All';
        $priceRange    = $this->request->getGet('price_range') ?? 'all';
        $sort          = $this->request->getGet('sort') ?? 'popular';

        $where  = [];
        $params = [];

        if ($search !== '') { $where[] = "name LIKE ?"; $params[] = "%$search%"; }
        if ($asideCategory !== '' && $asideCategory !== 'All') { $where[] = "category = ?"; $params[] = $asideCategory; }
        if ($priceRange !== '' && $priceRange !== 'all') {
            [$min, $max] = explode('-', $priceRange);
            $where[] = "price BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
        }

        $whereSql      = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $secondarySort = "id DESC";
        if ($sort === 'price-asc')  $secondarySort = "price ASC";
        elseif ($sort === 'price-desc') $secondarySort = "price DESC";
        elseif ($sort === 'rating') $secondarySort = "rating DESC";

        $orderBy  = "ORDER BY (stock = 0) ASC, $secondarySort";
        $products = $db->query("SELECT * FROM products $whereSql $orderBy", $params)->getResultArray();
        $categories = $db->query("SELECT DISTINCT category FROM products ORDER BY category")->getResultArray();
        $categories = array_column($categories, 'category');

        // -------------------------------------------------------
        // DATA KERANJANG
        // -------------------------------------------------------
        $cart      = session()->get('cart') ?? [];
        $cartItems = [];
        $cartCount = 0;
        $cartTotal = 0;

        if (!empty($cart)) {
            $pids = array_unique(array_map(fn($k) => (int)explode('|', $k)[0], array_keys($cart)));
            $placeholders = implode(',', array_fill(0, count($pids), '?'));
            $byId = [];
            foreach ($db->query("SELECT id, name, price, img FROM products WHERE id IN ($placeholders)", $pids)->getResultArray() as $r) {
                $byId[$r['id']] = $r;
            }
            foreach ($cart as $cartKey => $qty) {
                [$pid, $colour, $size] = explode('|', $cartKey) + [0, '', ''];
                if (!isset($byId[$pid])) continue;
                $item             = $byId[$pid];
                $item['cart_key'] = $cartKey;
                $item['colour']   = $colour;
                $item['size']     = $size;
                $item['qty']      = $qty;
                $item['subtotal'] = $item['price'] * $qty;
                $cartItems[]      = $item;
                $cartCount       += $qty;
                $cartTotal       += $item['subtotal'];
            }
        }

        // -------------------------------------------------------
        // PERBAIKAN: DATA WISHLIST (Aman dari Fatal Error PHP 8)
        // -------------------------------------------------------
        $wishlistCount = 0;
        $wishlisted = [];
        $user          = session()->get('user');
        
        if ($user) {
            // Menggunakan penamaan ALIAS 'total' dan dibaca lewat getRowArray() agar stabil
            $wCountRow = $db->query("
                SELECT COUNT(w.id) AS total FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?
            ", [$user['id']])->getRowArray();
            
            $wishlistCount = $wCountRow ? (int) $wCountRow['total'] : 0;

            // Wishlist per produk (untuk ikon hati)
            $wRows = $db->query("SELECT product_id FROM wishlist WHERE user_id = ?", [$user['id']])->getResultArray();
            foreach ($wRows as $wr) {
                $wishlisted[$wr['product_id']] = true;
            }
        }

        return view('home/index', [
            'products'      => $products,
            'categories'    => $categories,
            'cartItems'     => $cartItems,
            'cartCount'     => $cartCount,
            'cartTotal'     => $cartTotal,
            'wishlistCount' => $wishlistCount,
            'wishlisted'    => $wishlisted,
            'search'        => $search,
            'msg'           => $msg,
            'user'          => $user,
        ]);
    }

    public function getVariants()
    {
        $product_id = (int) ($this->request->getGet('product_id') ?? 0);

        if (!$product_id) {
            return $this->response->setJSON([]);
        }

        $db   = \Config\Database::connect();
        $rows = $db->query(
            "SELECT colour, size, stock FROM product_variants WHERE product_id = ? ORDER BY colour, size",
            [$product_id]
        )->getResultArray();

        // Format: { "Colour1": { "Size1": stock, "Size2": stock }, ... }
        $result = [];
        foreach ($rows as $row) {
            $result[$row['colour']][$row['size']] = (int) $row['stock'];
        }

        return $this->response->setJSON($result);
    }
}