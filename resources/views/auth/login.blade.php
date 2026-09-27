<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Bluewash Laundry</title>
    
    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
</head>
<body style="display:block; min-height: 100vh; overflow: hidden;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo-area">
                <img src="{{ asset('assets/logo.png') }}" alt="Bluewash Logo" class="auth-logo">
                <h2 class="auth-title">Bluewash Laundry</h2>
                <p class="auth-subtitle">Sistem Informasi POS Kasir Laundry</p>
            </div>
            
            {{-- Validasi Error --}}
            @if ($errors->any())
                <div class="alert-message error" style="display: flex;">
                    <div class="alert-message-content">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="this.closest('.alert-message').remove()" title="Tutup Notifikasi">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="username">Username Kasir / Admin</label>
                    <div class="input-container">
                        <i class="ri-user-line input-icon"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username..." value="{{ old('username') }}" required autofocus autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi (Password)</label>
                    <div class="input-container">
                        <i class="ri-lock-line input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password..." required autocomplete="current-password">
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility()">
                            <i id="passwordIcon" class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 10px;">
                    <i class="ri-login-box-line"></i>
                    <span>Masuk ke Sistem POS</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'ri-eye-off-line';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'ri-eye-line';
            }
        }
    </script>
</body>
</html>
