<?php

namespace App\Controllers;

class Riwayat extends BaseController
{
    // =================================================================
    // 1. FUNGSI UNTUK MENAMPILKAN HALAMAN RIWAYAT
    // =================================================================
    public function index()
    {
        $session = session();
        $user = $session->get('user');

        // Wajib login
        if (!$user) {
            $session->setFlashdata('msg', 'Silakan login terlebih dahulu.');
            $session->setFlashdata('msgType', 'warning');
            return redirect()->to(site_url('auth/login'));
        }

        $db = \Config\Database::connect();
        $userId = (int)$user['id'];

        // --- AMBIL SEMUA ORDER MILIK USER ---
        $orders = $db->table('orders')
            ->select('id, total, address, payment_method, payment_info, payment_status, shipping_status, created_at')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        // --- AMBIL ITEM PER ORDER ---
        $orderItems = [];
        if (!empty($orders)) {
            $orderIds = array_column($orders, 'id');
            $items = $db->table('order_items oi')
                ->select('oi.order_id, oi.qty, oi.price, p.name, p.img')
                ->join('products p', 'p.id = oi.product_id', 'left')
                ->whereIn('oi.order_id', $orderIds)
                ->get()->getResultArray();
                
            foreach ($items as $row) {
                $orderItems[$row['order_id']][] = $row;
            }
        }

        // --- CART COUNT & KATEGORI ---
        $cartCount = 0;
        $cart = $session->get('cart') ?? [];
        foreach ($cart as $qty) {
            $cartCount += $qty;
        }

        $catDb = $db->table('products')->select('category')->distinct()->orderBy('category')->get()->getResultArray();
        $categories = array_column($catDb, 'category');

        return view('riwayat', [
            'user'       => $user,
            'orders'     => $orders,
            'orderItems' => $orderItems,
            'cartCount'  => $cartCount,
            'categories' => $categories
        ]);
    }

    // =================================================================
    // 2. FUNGSI UNTUK MEMPROSES FORM (TERIMA / BATALKAN PESANAN)
    // =================================================================
    public function processAction()
    {
        $session = session();
        $user = $session->get('user');

        // Wajib login (untuk keamanan proses form)
        if (!$user) {
            $session->setFlashdata('msg', 'Silakan login terlebih dahulu.');
            $session->setFlashdata('msgType', 'warning');
            return redirect()->to(site_url('auth/login'));
        }

        $db = \Config\Database::connect();
        $userId = (int)$user['id'];
        
        $action  = $this->request->getPost('action');
        $orderId = (int)$this->request->getPost('order_id');

        // Aksi Terima Pesanan
        if ($action === 'terima_pesanan') {
            $updated = $db->table('orders')
                ->where(['id' => $orderId, 'user_id' => $userId])
                ->update(['payment_status' => 'paid', 'shipping_status' => 'selesai']);
            
            if ($updated) {
                $session->setFlashdata('flashMsg', 'Pesanan telah dikonfirmasi selesai. Terima kasih!');
                $session->setFlashdata('flashType', 'success');
            }
            return redirect()->to(site_url('riwayat'));

        // Aksi Batalkan Pesanan
        } elseif ($action === 'batalkan_pesanan') {
            // Cek status pesanan saat ini
            $orderRow = $db->table('orders')
                ->select('payment_status, shipping_status')
                ->where(['id' => $orderId, 'user_id' => $userId])
                ->get()->getRowArray();

            $allowedToBatal = false;
            if ($orderRow) {
                $ps = $orderRow['payment_status'];
                $ss = strtolower($orderRow['shipping_status'] ?? '');
                
                // Kondisi boleh batal: pending_cod, paid, unpaid, pending DAN belum diproses/dikirim
                $validPayment = in_array($ps, ['pending_cod', 'paid', 'unpaid', 'pending']);
                $invalidShipping = in_array($ss, ['diproses', 'dikirim', 'selesai', 'sampai']);
                
                if ($validPayment && !$invalidShipping) {
                    $allowedToBatal = true;
                }
            }

            if ($allowedToBatal) {
                $cancelItems = $db->table('order_items')
                    ->select('product_id, qty, colour, size')
                    ->where('order_id', $orderId)
                    ->get()->getResultArray();

                $db->transStart(); // Mulai transaksi DB
                try {
                    foreach ($cancelItems as $ci) {
                        // Kembalikan stok varian
                        $db->query("UPDATE product_variants SET stock = stock + ? WHERE product_id = ? AND colour = ? AND size = ?", 
                            [$ci['qty'], $ci['product_id'], $ci['colour'], $ci['size']]);
                        // Perbarui total stok produk
                        $db->query("UPDATE products SET stock = (SELECT COALESCE(SUM(stock),0) FROM product_variants WHERE product_id = ?) WHERE id = ?", 
                            [$ci['product_id'], $ci['product_id']]);
                    }

                    // Simpan status dibatalkan
                    $db->table('orders')
                        ->where(['id' => $orderId, 'user_id' => $userId])
                        ->update(['payment_status' => 'cancelled', 'shipping_status' => 'cancelled']);

                    $db->transCommit();
                    $session->setFlashdata('flashMsg', 'Pesanan berhasil dibatalkan. Stok produk telah dikembalikan.');
                    $session->setFlashdata('flashType', 'success');
                } catch (\Exception $e) {
                    $db->transRollback();
                    $session->setFlashdata('flashMsg', 'Gagal membatalkan pesanan: ' . $e->getMessage());
                    $session->setFlashdata('flashType', 'error');
                }
            } else {
                $session->setFlashdata('flashMsg', 'Pesanan tidak dapat dibatalkan karena sudah diproses atau dikirim.');
                $session->setFlashdata('flashType', 'error');
            }
            
            return redirect()->to(site_url('riwayat'));
        }

        // Jika aksi tidak dikenali
        return redirect()->to(site_url('riwayat'));
    }
}