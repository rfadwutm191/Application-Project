@extends('layouts.app')

@section('title', 'Profile Saya - Bluewash Laundry')
@section('header_title', 'Profile Saya')
@section('header_subtitle', 'Kelola informasi profil dan kata sandi akun Anda')

@section('content')
<div class="panel-card" style="width: 100%; max-width: 100%; margin-bottom: 30px;">
    <div class="panel-header" style="margin-bottom: 15px;">
        <h3 class="panel-title">
            <i class="ri-user-settings-line"></i>
            <span>Pengaturan Akun</span>
        </h3>
    </div>

    @if(session('success'))
        <div class="alert-message success" style="display: flex; margin-bottom: 20px;">
            <div class="alert-message-content">
                <i class="ri-checkbox-circle-line"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="alert-close-btn" onclick="this.closest('.alert-message').remove()" title="Tutup Notifikasi">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-message error" style="display: flex; margin-bottom: 20px;">
            <div class="alert-message-content">
                <i class="ri-error-warning-line"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            <button type="button" class="alert-close-btn" onclick="this.closest('.alert-message').remove()" title="Tutup Notifikasi">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    <form action="{{ route('customer.profile.update') }}" method="POST" class="auth-form">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <h4 style="color: var(--primary); font-size: 14px; border-bottom: 1px solid rgba(15,23,42,0.08); padding-bottom: 5px; margin: 0;">
                    Detail Kontak
                </h4>

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-container">
                        <i class="ri-user-line input-icon"></i>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">No. WhatsApp</label>
                    <div class="input-container">
                        <i class="ri-phone-line input-icon"></i>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap Penjemputan</label>
                    <div class="input-container">
                        <i class="ri-map-pin-line input-icon" style="top: 18px;"></i>
                        <textarea id="address" name="address" class="form-control" style="height: 80px; padding-left: 46px; resize: none;" required>{{ old('address', $customer->address) }}</textarea>
                    </div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <h4 style="color: var(--primary); font-size: 14px; border-bottom: 1px solid rgba(15,23,42,0.08); padding-bottom: 5px; margin: 0;">
                    Keamanan Akun
                </h4>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <div class="input-container">
                        <i class="ri-lock-line input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah...">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <div class="input-container">
                        <i class="ri-lock-password-line input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password...">
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: center; margin-top: 25px; border-top: 1px solid rgba(15,23,42,0.06); padding-top: 20px;">
            <button type="submit" class="btn-primary" style="padding: 12px 40px; min-width: 420px;">
                <i class="ri-save-line"></i>
                <span>Simpan Perubahan Profil</span>
            </button>
        </div>
    </form>
</div>
@endsection
