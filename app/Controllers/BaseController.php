<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Properti session untuk menampung layanan session CodeIgniter.
     */
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Aktifkan helper sebelum parent init
        $this->helpers = ['form', 'url'];

        // Menjalankan init utama dari CodeIgniter
        parent::initController($request, $response, $logger);

        // Mengaktifkan session menggunakan fungsi bawaan service() yang lebih aman
        $this->session = service('session');
    }
}