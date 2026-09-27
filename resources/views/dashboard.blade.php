@extends('layouts.app')

@section('title', 'Dashboard - Bluewash Laundry')
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Tinjauan operasional laundry dan ringkasan transaksi hari ini')

@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y H:i') : '-';
    $statusLabel = fn($s) => match(true) {
        in_array($s, ['ready', 'siap_diambil']) => 'Siap Diambil',
        in_array($s, ['completed', 'selesai'])   => 'Selesai',
        in_array($s, ['rejected', 'ditolak'])    => 'Ditolak',
        default                                   => 'Diproses',
    };
@endphp

@section('content')
<div class="stats-grid">
    <div class="stat-card cyan">
        <div class="stat-info">
            <span class="stat-label">Pesanan Aktif</span>
            <span class="stat-value">{{ $activeOrdersCount }}</span>
        </div>
        <div class="stat-icon-wrapper cyan">
            <i class="ri-loader-3-line"></i>
        </div>
    </div>
    
    <div class="stat-card green">
        <div class="stat-info">
            <span class="stat-label">Selesai Hari Ini</span>
            <span class="stat-value">{{ $completedTodayCount }}</span>
        </div>
        <div class="stat-icon-wrapper green">
            <i class="ri-checkbox-circle-line"></i>
        </div>
    </div>

    @if(Auth::user()->isAdmin())
        <div class="stat-card blue">
            <div class="stat-info">
                <span class="stat-label">Total Pendapatan</span>
                <span class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="stat-icon-wrapper blue">
                <i class="ri-wallet-2-line"></i>
            </div>
        </div>
    @endif

    <div class="stat-card purple">
        <div class="stat-info">
            <span class="stat-label">Pesanan Masuk Hari Ini</span>
            <span class="stat-value">{{ $newOrdersToday }}</span>
        </div>
        <div class="stat-icon-wrapper purple">
            <i class="ri-add-box-line"></i>
        </div>
    </div>
</div>

<div class="table-card" style="margin-top: 10px;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 14px 14px 0 0; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="margin: 0; font-size: 17px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="ri-shopping-basket-2-line" style="color: #2563eb;"></i>
                <span>Daftar Transaksi Terbaru</span>
            </h3>
            <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">Memuat transaksi laundry terbaru dalam sistem</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('orders.index') }}" class="btn-secondary" style="padding: 8px 16px; font-size: 12px; border-radius: 10px; font-weight: 600; text-decoration: none;">
                <i class="ri-file-list-line"></i> Lihat Semua Transaksi
            </a>
            @if(Auth::user()->isStaff())
                <a href="{{ route('orders.create') }}" class="btn-primary" style="padding: 8px 16px; font-size: 12px; border-radius: 10px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i class="ri-add-line"></i> Transaksi Baru
                </a>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="bw-table datatable">
            <thead>
                <tr>
                    <th>Kode Nota</th>
                    <th>Pelanggan</th>
                    <th>Layanan Laundry</th>
                    <th>Detail Cucian</th>
                    <th>Total Biaya</th>
                    <th>Status Cucian</th>
                    <th>Tanggal Masuk</th>
                    <th>Estimasi Selesai</th>
                    <th style="width: 130px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);">
                            {{ $order->order_code }}
                            @if($order->is_express)
                                <span class="badge badge-express" style="font-size: 9px; padding: 2px 6px; margin-left: 2px;">EXPRESS</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $order->customer_name }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $order->customer_phone }}</div>
                        </td>
                        <td>{{ $order->service_name }}</td>
                        <td>
                            @if($order->detailOrders && $order->detailOrders->count() > 1)
                                {{ $order->detailOrders->map(fn($d) => $d->service_type === 'kg' ? ($d->weight > 0 ? number_format($d->weight, 1).' Kg' : 'Timbangan') : $d->qty.' Pcs')->join(', ') }}
                            @elseif($order->service_type === 'kg')
                                {{ $order->weight == 0 ? 'Menunggu Timbangan' : number_format($order->weight, 1) . ' Kg' }}
                            @else
                                {{ $order->qty }} Pcs
                            @endif
                        </td>
                        <td style="font-weight: 700; color: var(--text-main);">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td>{{ $statusLabel($order->status) }}</td>
                        <td>{{ $fmt($order->date_in ?? $order->created_at) }}</td>
                        <td style="color: var(--text-main);">{{ $fmt($order->date_out) }}</td>
                        <td>
                            <div class="action-buttons-cell">
                                <button type="button" onclick="printReceiptIframe({{ $order->id }})" class="btn-icon print" title="Cetak Nota Resi">
                                    <i class="ri-printer-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
