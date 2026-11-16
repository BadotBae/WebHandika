<?php

namespace App\Controllers;

class Wishlist extends BaseController
{
    // Halaman Utama My Wishlist (GET /wishlist)
    public function index()
    {
        $db = \Config\Database::connect();
        $session = session();
        $user = $session->get('user');

        // Proteksi jika belum login (jika filter 'auth' belum aktif)
        if (!$user) {
            return redirect()->to(site_url('auth/login'));
        }

        $userId = (int)$user['id'];

        // 1. AMBIL DATA WISHLIST (Query Join dengan produk)
        $rawWishlistItems = $db->table('wishlist w')
            ->select('p.id, p.name, p.price, p.img, p.stock, p.category')
            ->join('products p', 'p.id = w.product_id')
            ->where('w.user_id', $userId)
            ->orderBy('w.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // 2. FILTER & AUTO-REMOVE ITEM YANG SUDAH ADA DI KERANJANG
        $cart = $session->get('cart') ?? [];
        $cartProductIds = [];
        foreach ($cart as $cartKey => $qty) {
            // Memisahkan format "product_id|colour|size"
            $parts = explode('|', $cartKey);
            $cartProductIds[] = (int)$parts[0];
        }

        $wishlistItems = [];
        foreach ($rawWishlistItems as $item) {
            if (in_array((int)$item['id'], $cartProductIds)) {
                // Jika sudah ada di keranjang, hapus dari tabel wishlist otomatis
                $db->table('wishlist')
                    ->where('user_id', $userId)
                    ->where('product_id', (int)$item['id'])
                    ->delete();
            } else {
                $wishlistItems[] = $item;
            }
        }

        // 3. HITUNG CART COUNT UNTUK BADGE
        $cartCount = 0;
        foreach ($cart as $qty) {
            $cartCount += $qty;
        }

        // 4. AMBIL KATEGORI UNTUK NAVBAR DROPDOWN
        $categoriesData = $db->table('products')
            ->select('category')
            ->distinct()
            ->orderBy('category', 'ASC')
            ->get()
            ->getResultArray();
        $categories = array_column($categoriesData, 'category');

        // Kirim data ke view
        return view('wishlist', [
            'wishlistItems' => $wishlistItems,
            'cartCount'     => $cartCount,
            'categories'    => $categories,
            'user'          => $user
        ]);
    }

    // Toggle Wishlist (POST /wishlist) - Dipanggil dari tombol heart beranda
    public function toggle()
    {
        $db = \Config\Database::connect();
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            return redirect()->to(site_url('auth/login'));
        }

        $userId    = (int)$user['id'];
        $productId = (int)$this->request->getPost('product_id');
        $redirect  = $this->request->getPost('redirect') ?? 'wishlist';

        // Cek apakah sudah ada di wishlist
        $check = $db->table('wishlist')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->countAllResults();

        if ($check > 0) {
            // Sudah ada → hapus (toggle off)
            $db->table('wishlist')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();
            $session->setFlashdata('msg', 'Produk dihapus dari wishlist.');
            $session->setFlashdata('msgType', 'warning');
        } else {
            // Belum ada → tambah (toggle on)
            $db->table('wishlist')->insert([
                'user_id'    => $userId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $session->setFlashdata('msg', 'Produk berhasil ditambahkan ke wishlist!');
            $session->setFlashdata('msgType', 'success');
        }

        return redirect()->to(site_url($redirect));
    }

    // Hapus dari halaman wishlist (GET /wishlist/remove/ID)
    public function remove($productId)
    {
        $db = \Config\Database::connect();
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            return redirect()->to(site_url('auth/login'));
        }

        $userId = (int)$user['id'];

        $db->table('wishlist')
            ->where('user_id', $userId)
            ->where('product_id', (int)$productId)
            ->delete();

        $session->setFlashdata('msg', 'Produk berhasil dihapus dari wishlist.');
        $session->setFlashdata('msgType', 'info');

        return redirect()->to(site_url('wishlist'));
    }
}