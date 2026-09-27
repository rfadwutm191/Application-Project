<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pelanggan - Bluewash Laundry</title>
    
    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    
    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body style="display:block; min-height: 100vh; overflow: hidden;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo-area">
                <img src="{{ asset('assets/logo.png') }}" alt="Bluewash Logo" class="auth-logo">
                <h2 class="auth-title">Daftar Akun Pelanggan</h2>
                <p class="auth-subtitle">Registrasi akun baru untuk memesan dan melacak laundry Anda</p>
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

            <form action="{{ route('register') }}" method="POST" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-container">
                        <i class="ri-user-smile-line input-icon"></i>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap..." value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username Akun</label>
                    <div class="input-container">
                        <i class="ri-user-line input-icon"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Tentukan username untuk login..." value="{{ old('username') }}" required autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">No. Telepon / WhatsApp</label>
                    <div class="input-container">
                        <i class="ri-phone-line input-icon"></i>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Contoh: 08123456789..." value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <div class="input-container">
                        <i class="ri-map-pin-line input-icon" style="top: 18px;"></i>
                        <textarea id="address" name="address" class="form-control" style="height: 60px; padding-left: 46px; resize: none;" placeholder="Alamat rumah/kost..." required>{{ old('address') }}</textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi (Password)</label>
                    <div class="input-container">
                        <i class="ri-lock-line input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter..." required autocomplete="new-password">
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password', 'passwordIcon')">
                            <i id="passwordIcon" class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="input-container">
                        <i class="ri-lock-check-line input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password..." required autocomplete="new-password">
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password_confirmation', 'passwordConfirmIcon')">
                            <i id="passwordConfirmIcon" class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 10px;">
                    <i class="ri-user-add-line"></i>
                    <span>Daftar Akun Pelanggan</span>
                </button>
            </form>
            
            <div class="auth-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
            
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
