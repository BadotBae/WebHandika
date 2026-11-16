<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');

        // Jika belum login ATAU role_id bukan 1 (Bukan Admin)
        if (!$user || $user['role_id'] != 1) {
            return redirect()->to(base_url('/'))->with('error', 'Akses ditolak! Anda bukan Admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosongkan saja
    }
}