@extends('layouts.app')

@section('title', 'Cetak Struk Transaksi - Bluewash Laundry')
@section('header_title', 'Cetak Struk Transaksi')
@section('header_subtitle', 'Pencarian nota transaksi untuk dicetak ulang resi thermal')

@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d M Y H:i') : '-';
    $badge = function($status) {
        return match($status) {
            'queued'                  => ['bg' => '#f1f5f9', 'c' => '#475569', 'label' => 'Antrean'],
            'processing', 'confirmed' => ['bg' => '#eff6ff', 'c' => '#2563eb', 'label' => 'Diproses'],
            'ready'                   => ['bg' => '#fefce8', 'c' => '#ca8a04', 'label' => 'Siap Diambil'],
            'completed'               => ['bg' => '#f0fdf4', 'c' => '#16a34a', 'label' => 'Selesai'],
            default                   => ['bg' => '#fef2f2', 'c' => '#dc2626', 'label' => 'Ditolak'],
        };
    };
@endphp

@section('content')
<div style="width: 100%; margin: 0;">
    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 24px;">
        <form action="{{ route('orders.print_search') }}" method="GET" style="display: flex; gap: 12px; align-items: center;">
            <div style="position: relative; flex: 1;">
                <i class="ri-search-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Kode Nota (misal: BW-1001), Nama, atau No. HP Pelanggan..." class="form-control" style="width: 100%; padding: 12px 14px 12px 42px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px;">
            </div>
            <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 12px 24px; border-radius: 10px; border: none; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="ri-search-line"></i> Cari Nota
            </button>
        </form>
    </div>

    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow: hidden;">
        <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; font-size: 15px; color: #1e293b; font-weight: 700;">
                <i class="ri-file-text-line" style="color: #2563eb;"></i> {{ $search ? "Hasil Pencarian: \"{$search}\"" : "Daftar Transaksi Terbaru" }}
            </h3>
            <span style="font-size: 12px; color: #64748b; font-weight: 600;">Total: {{ $orders->count() }} Transaksi</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="datatable" style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead>
                    <tr style="background: #f1f5f9; color: #475569; font-weight: 700; font-size: 12px; letter-spacing: 0.02em; text-align: center;">
                        <th style="padding: 14px 20px; text-align: center;">No. Nota</th>
                        <th style="padding: 14px 20px; text-align: center;">Pelanggan</th>
                        <th style="padding: 14px 20px; text-align: center;">Layanan</th>
                        <th style="padding: 14px 20px; text-align: center;">Total Biaya</th>
                        <th style="padding: 14px 20px; text-align: center;">Tanggal Masuk</th>
                        <th style="padding: 14px 20px; text-align: center;">Status</th>
                        <th style="padding: 14px 20px; text-align: center;">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php $st = $badge($order->status); @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px; font-weight: 700; color: #2563eb;">{{ $order->order_code }}</td>
                            <td style="padding: 14px 20px;">
                                <div style="font-weight: 600; color: #0f172a;">{{ $order->customer_name }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $order->customer_phone }}</div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <span style="font-weight: 600; color: #334155;">{{ $order->service_name }}</span>
                                @if($order->is_express)
                                    <span style="background: #fef3c7; color: #b45309; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">EXPRESS</span>
                                @endif
                            </td>
                            <td style="padding: 14px 20px; font-weight: 700; color: #0f172a;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td style="padding: 14px 20px; color: #64748b; font-size: 12px;">
                                {{ $fmt($order->date_in ?? $order->created_at) }}
                            </td>
                            <td style="padding: 14px 20px; text-align: center;">
                                <span style="background: {{ $st['bg'] }}; color: {{ $st['c'] }}; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 20px;">{{ $st['label'] }}</span>
                            </td>
                            <td style="padding: 14px 20px; text-align: center;">
                                <button type="button" onclick="printReceiptIframe({{ $order->id }})" class="btn" style="background: #059669; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <i class="ri-printer-line"></i> Cetak Resi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">
                                <i class="ri-inbox-line" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                                Tidak ditemukan data transaksi yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
