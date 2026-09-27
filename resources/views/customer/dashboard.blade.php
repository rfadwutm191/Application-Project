@extends('layouts.app')

@section('title', 'Dashboard Pelanggan - Bluewash Laundry')
@section('header_title', 'Dashboard Pelanggan')
@section('header_subtitle', 'Tinjauan status pesanan laundry Anda')

@section('content')
{{-- Welcome Banner --}}
<div class="panel-card" style="margin-bottom: 25px; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border: none;">
    <div style="display: flex; align-items: center; gap: 20px; padding: 10px;">
        <div style="font-size: 40px; line-height: 1;">👋</div>
        <div>
            <h2 style="color: #0f172a; margin-bottom: 4px;">Selamat Datang, {{ $customer->name }}!</h2>
            <p style="color: #334155; font-size: 14px; margin: 0;">Terima kasih telah memilih Bluewash Laundry. Semua status pengerjaan pakaian Anda dapat dipantau langsung di menu <strong>Riwayat Pesanan</strong>.</p>
        </div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card cyan">
        <div class="stat-info">
            <span class="stat-label">Cucian Sedang Diproses</span>
            <span class="stat-value">{{ $activeOrdersCount }}</span>
        </div>
        <div class="stat-icon-wrapper cyan">
            <i class="ri-loader-3-line"></i>
        </div>
    </div>
    
    <div class="stat-card green">
        <div class="stat-info">
            <span class="stat-label">Total Cucian Selesai</span>
            <span class="stat-value">{{ $completedOrdersCount }}</span>
        </div>
        <div class="stat-icon-wrapper green">
            <i class="ri-checkbox-circle-line"></i>
        </div>
    </div>
</div>
@endsection
