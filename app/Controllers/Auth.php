<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user')) {
            return redirect()->to(base_url('/'));
        }

        // PERBAIKAN: Menggunakan $this->request->is('post') agar mendeteksi POST dengan benar di semua versi CI4
        if ($this->request->is('post')) {
            $email = trim($this->request->getPost('email') ?? '');
            $pass  = $this->request->getPost('password') ?? '';

            if ($email === '' || $pass === '') {
                return redirect()->back()->withInput()->with('error', 'Email dan password wajib diisi.');
            } 
            
            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($pass, $user['password'])) {
                session()->set('user', [
                    'id'      => $user['id'],
                    'name'    => $user['name'],
                    'email'   => $user['email'],
                    'role_id' => $user['role_id'],
                    'address' => $user['address'] ?? ''
                ]);

                // Redirect berdasarkan role_id
                if ($user['role_id'] == 1) {
                    return redirect()->to(base_url('admin/dashboard'));
                } else {
                    return redirect()->to(base_url('/'));
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
            }
        }

        return view('auth/login');
    }

    public function register()
    {
        if (session()->get('user')) {
            return redirect()->to(base_url('/'));
        }

        // PERBAIKAN: Menggunakan $this->request->is('post')
        if ($this->request->is('post')) {
            $name    = trim($this->request->getPost('name') ?? '');
            $email   = trim($this->request->getPost('email') ?? '');
            $address = trim($this->request->getPost('address') ?? '');
            $pass    = $this->request->getPost('password') ?? '';

            if ($name === '' || $email === '' || $pass === '' || $address === '') {
                return redirect()->back()->withInput()->with('error', 'Semua kolom wajib diisi.');
            } 
            
            if (!str_ends_with(strtolower($email), '@gmail.com')) {
                return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal: Email harus menggunakan @gmail.com.');
            } 
            
            $userModel = new UserModel();
            $existing  = $userModel->where('email', $email)->first();

            if ($existing) {
                return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar.');
            } 
            
            $inserted = $userModel->insert([
                'name'     => $name,
                'email'    => $email,
                'password' => password_hash($pass, PASSWORD_DEFAULT),
                'address'  => $address,
                'role_id'  => 2,
            ]);

            if ($inserted) {
                return redirect()->to(site_url('auth/login'))->with('success', 'Pendaftaran berhasil! Silakan login.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mendaftar. Silakan coba lagi.');
            }
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->remove('user');
        return redirect()->to(site_url('auth/login'))->with('success', 'Anda berhasil logout.');
    }
}