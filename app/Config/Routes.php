<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =================================================================
// 1. RUTE PUBLIK (Bisa diakses tanpa login)
// =================================================================
$routes->get('/', 'Home::index');
$routes->post('/', 'Home::index');
$routes->get('get-variants', 'Home::getVariants'); // AJAX pilih warna/ukuran

// Keranjang Belanja (View & Update keranjang publik, checkout butuh login)
$routes->get('cart', 'Cart::index');
$routes->post('cart/action', 'Cart::processAction'); // Handle: inc, dec, update, remove, clear

// Autentikasi
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');


// =================================================================
// 2. RUTE CUSTOMER (Wajib Login - Filter 'auth')
// =================================================================
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Proses Checkout Keranjang
    $routes->post('checkout', 'Cart::checkout');

    // Riwayat Pesanan
    $routes->get('riwayat',  'Riwayat::index');
    $routes->post('riwayat', 'Riwayat::processAction'); // Handle: terima_pesanan & batalkan_pesanan
    

    // Wishlist
    $routes->get('wishlist', 'Wishlist::index');
    $routes->post('wishlist', 'Wishlist::toggle'); // Handle POST tambah/hapus
    $routes->get('wishlist/remove/(:num)', 'Wishlist::remove/$1'); // Handle GET parameter ?remove=id
});


// =================================================================
// 3. RUTE ADMIN (Wajib Login & Role Admin - Filter 'admin')
// =================================================================
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    // Rute utama admin (misal: localhost:8080/admin)
    $routes->get('/',             'Admin::index');
    $routes->get('dashboard',     'Admin::index'); 
    
    // Manajemen Pesanan
    $routes->get('orders',               'Admin::orders');
    
    // Rute untuk mengubah status pengiriman via link shortcut dropdown halaman utama orders
    $routes->get('update_shipping/(:num)/(:any)', 'Admin::updateShippingStatus/$1/$2');
    
    // Rute untuk menampilkan halaman detail pesanan (Mendukung format order_detail dan orders/detail)
    $routes->get('order_detail/(:num)',   'Admin::orderDetail/$1');
    $routes->get('orders/detail/(:num)',  'Admin::orderDetail/$1'); 
    
    // Rute untuk menangani proses simpan/update form di dalam halaman detail pesanan
    $routes->post('order_detail/(:num)',  'Admin::orderDetail/$1');
    $routes->post('orders/detail/(:num)', 'Admin::orderDetail/$1'); 
    
    // Verifikasi Pembayaran
    $routes->get('verification',         'Admin::verification');
    $routes->post('verification/process', 'Admin::processVerification'); 
    
    // Manajemen Produk
    $routes->get('products',      'Admin::products');
    $routes->post('products',     'Admin::products');
    
    // Manajemen Users
    $routes->get('users',         'Admin::users');
    $routes->post('users',        'Admin::users');
    
    // Laporan Penjualan
    $routes->get('laporan',       'Admin::laporan');
});