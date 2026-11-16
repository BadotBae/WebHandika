<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun – Toko Helm Handika</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: url('<?= base_url('img/FromLD.png') ?>') center/cover no-repeat fixed;
            background-color: #2b2b2b;
            position: relative;
            z-index: 1;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
        }

        .card-header { margin-bottom: 2rem; }

        .card-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
            letter-spacing: 0.5px;
        }

        .card-header p {
            font-size: 13px;
            color: #f1f5f9;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .field {
            margin-bottom: 1.25rem;
            text-align: left;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .input-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
            background: #ffffff;
            padding: 0 14px;
            height: 46px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        
        .input-wrap:focus-within {
            box-shadow: 0 0 0 3px rgba(214, 9, 29, 0.4);
        }

        .textarea-wrap {
            height: auto;
            min-height: 80px;
            align-items: flex-start;
            padding-top: 14px;
            padding-bottom: 14px;
        }

        .input-wrap svg {
            flex-shrink: 0;
            color: #64748b;
        }

        .input-wrap input,
        .input-wrap textarea {
            border: none;
            background: transparent;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            width: 100%;
            height: 100%;
            outline: none;
        }

        .input-wrap textarea { resize: vertical; }

        .input-wrap input::placeholder,
        .input-wrap textarea::placeholder { color: #94a3b8; }

        .eye-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            color: #64748b;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        
        .eye-btn:hover {
            color: #1e293b;
        }

        .btn-login {
            width: 100%;
            height: 46px;
            margin-top: 0.5rem;
            background: #d6091d;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .btn-login:hover { background: #d73939; }

        .extra-links {
            margin-top: 1.5rem;
            font-size: 13px;
            color: #f1f5f9;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            text-align: center;
        }

        .extra-links a {
            color: #ffffff;
            font-weight: 600;
            text-decoration: none;
        }

        .extra-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <h1>TOKO HELM HANDIKA</h1>
        <p>Buat akun baru untuk mulai berbelanja</p>
    </div>

    <form method="post" action="<?= site_url('auth/register') ?>" id="registerForm">
        <?= csrf_field() ?>

        <div class="field">
            <label for="name">Nama Lengkap</label>
            <div class="input-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" value="<?= esc($old_name ?? '') ?>" required>
            </div>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <div class="input-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <input type="email" id="email" name="email" placeholder="contoh@gmail.com" value="<?= esc($old_email ?? '') ?>" required autocomplete="email">
            </div>
        </div>

        <div class="field">
            <label for="address">Alamat Pengiriman</label>
            <div class="input-wrap textarea-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <textarea id="address" name="address" rows="2" placeholder="Jl. Contoh No. 123, Kota..." required><?= esc($old_address ?? '') ?></textarea>
            </div>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="input-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input type="password" id="password" name="password" placeholder="Buat password baru" required autocomplete="new-password">
                <button type="button" class="eye-btn" onclick="togglePass()" aria-label="Tampilkan password">
                    <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login">Daftar Sekarang</button>
    </form>

    <div class="extra-links">
        Sudah punya akun? <a href="<?= site_url('auth/login') ?>">Login di sini</a>
    </div>
</div>

<script>
// Perbaikan: Fungsi Toggle Password kini mengubah icon secara dinamis (Eye / Eye-Off)
function togglePass() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        // Mengubah SVG menjadi ikon mata dicoret (Eye-off)
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        input.type = 'password';
        // Mengembalikan ke ikon mata biasa
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}
</script>

<?php if (!empty($err)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Pendaftaran Gagal',
            text: '<?= esc($err) ?>',
            confirmButtonColor: '#d6091d',
            confirmButtonText: 'Coba Lagi'
        });
    });
</script>
<?php endif; ?>

</body>
</html>