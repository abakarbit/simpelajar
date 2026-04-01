<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIMPEL-AJAR - Sistem Monitoring Dokumen Pembelajaran Teknik Biosistem ITERA. Platform untuk monitoring dan validasi dokumen pembelajaran secara efisien dan terintegrasi.">
    <meta name="keywords" content="monitoring dokumen, pembelajaran, Teknik Biosistem, ITERA, dosen, sistem informasi">
    <meta name="author" content="SIMPEL-AJAR">
    <meta name="theme-color" content="#1a73e8">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="SIMPEL-AJAR - Sistem Monitoring Dokumen Pembelajaran Teknik Biosistem ITERA">
    <meta property="og:description" content="Platform terintegrasi untuk monitoring dan validasi dokumen pembelajaran dengan mudah dan efisien.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">

    <!-- Favicon & Icons -->
    <link rel="icon" href="{{asset('storage/images/TBS.png')}}" type="image/png">
    <link rel="apple-touch-icon" href="{{asset('storage/images/TBS.png')}}">

    <title>Login — SIMPELAJAR | Sistem Monitoring Dokumen Pembelajaran Teknik Biosistem ITERA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1e2a3a 0%, #1a73e8 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 420px; border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .login-header { background: linear-gradient(135deg, #1e2a3a, #1a73e8); color: white; border-radius: 16px 16px 0 0; padding: 2rem; text-align: center; }
        .login-header h3 { font-weight: 800; letter-spacing: 1px; margin: 0; }
        .login-header p { opacity: .8; font-size: .85rem; margin: 4px 0 0; }
        .form-control:focus { border-color: #1a73e8; box-shadow: 0 0 0 .2rem rgba(26,115,232,.25); }
        .btn-primary { background: #1a73e8; border-color: #1a73e8; }
        .btn-primary:hover { background: #1557b0; }
        .demo-accounts { background: #f8f9fa; border-radius: 8px; font-size: .8rem; }
    </style>
</head>
<body>
<div class="login-card card">
    <div class="login-header">
        <!-- image -->
        <img src="{{asset('storage/images/ITERA.png')}}" width="100" class="mt-3 mb-2">
        <img src="{{asset('storage/images/TBS.png')}}" width="100" class="mt-3 mb-2">
        <h3 class="mt-2">SIMPELAJAR</h3>
        <p>Sistem Monitoring Dokumen Pembelajaran <br> Teknik Biosistem ITERA</p>
    </div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ $errors->first() }}
        </div>
        @endif
        @if(session('status'))
        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                        placeholder="email@institusi.ac.id" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        {{-- <div class="demo-accounts p-3 mt-4">
            <div class="fw-semibold mb-2 small text-muted"><i class="bi bi-info-circle me-1"></i>Akun Demo</div>
            <div class="mb-1"><span class="badge bg-warning text-dark me-1">GKMP</span> <code>gkmp@simpelajar.id</code></div>
            <div class="mb-1"><span class="badge bg-info me-1">Dosen</span> <code>budi@simpelajar.id</code></div>
            <div class="text-muted" style="font-size:.75rem">Password: <code>password</code></div>
        </div> --}}
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
