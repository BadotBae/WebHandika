<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        // Pengecekan Admin
        if (!$session->has('user') || $session->get('user')['role_id'] != 1) {
            return redirect()->to(base_url('auth/login'));
        }

        $db = \Config\Database::connect();
        $data = [];

        // 1. Omset Lunas (Hari Ini)
        $queryTodaySales = $db->query("SELECT SUM(total) AS total_sales FROM orders WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'");
        $data['todaySales'] = $queryTodaySales->getRow()->total_sales ?? 0;

        // 2. Omset Bulan Ini
        $queryMonthSales = $db->query("SELECT SUM(total) AS total_sales FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND payment_status = 'paid'");
        $data['monthSales'] = $queryMonthSales->getRow()->total_sales ?? 0;

        // 3. Total Transaksi Hari Ini
        $queryTodayTrx = $db->query("SELECT COUNT(*) AS total_trx FROM orders WHERE DATE(created_at) = CURDATE()");
        $data['todayTrx'] = $queryTodayTrx->getRow()->total_trx ?? 0;

        // 4. Stok Menipis
        $queryLowStock = $db->query("SELECT COUNT(*) AS low_stock FROM products WHERE stock <= 5");
        $data['lowStock'] = $queryLowStock->getRow()->low_stock ?? 0;

        // 5. Butuh Verifikasi
        $queryNeedVerif = $db->query("SELECT COUNT(*) AS need_verif FROM orders WHERE payment_status IN ('unpaid', 'pending', 'pending_cod')");
        $data['needVerif'] = $queryNeedVerif->getRow()->need_verif ?? 0;

        // 6. Data Chart (7 Hari Terakhir)
        $labels = [];
        $totals = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            // Mengambil total per hari, abaikan yang di-cancel
            $queryChart = $db->query("SELECT SUM(total) AS daily_total FROM orders WHERE DATE(created_at) = ? AND payment_status = 'paid'", [$date]);
            $val = $queryChart->getRow()->daily_total ?? 0;
            
            $labels[] = date('d M', strtotime($date));
            $totals[] = $val;
        }
        $data['labels'] = $labels;
        $data['totals'] = $totals;

        // 7. Produk Terlaris
        $data['topProds'] = $db->query("
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
        $data['recentOrders'] = $db->query("
            SELECT o.*, u.name AS user_name 
            FROM orders o 
            JOIN users u ON u.id = o.user_id
            ORDER BY o.created_at DESC 
            LIMIT 5
        ")->getResultArray();

        // Mengirim data ke View
        return view('admin/dashboard_view', $data);
    }
}