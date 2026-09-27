@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Bluewash Laundry')
@section('header_title', 'Riwayat Pesanan')
@section('header_subtitle', 'Pantau dan lihat seluruh riwayat transaksi laundry Anda')

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h3 class="panel-title">
            <i class="ri-file-list-3-line"></i>
            <span>Riwayat Pesanan Saya</span>
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

    {{-- Tabs --}}
    <div style="display: flex; gap: 4px; border-bottom: 2px solid rgba(15,23,42,0.08); margin-bottom: 0;">
        <button class="history-tab active" id="tab-active" onclick="switchTab('active')">
            <i class="ri-radar-line"></i>
            Pesanan Aktif
            <span class="tab-badge" id="badge-active">
                {{ $orders->whereNotIn('status', ['completed'])->count() }}
            </span>
        </button>
        <button class="history-tab" id="tab-completed" onclick="switchTab('completed')">
            <i class="ri-checkbox-circle-line"></i>
            Pesanan Selesai
            <span class="tab-badge" id="badge-completed" style="background: rgba(5,150,105,0.12); color: #059669;">
                {{ $orders->where('status', 'completed')->count() }}
            </span>
        </button>
    </div>

    {{-- Tab Content: Pesanan Aktif --}}
    <div id="panel-active" class="tab-panel">
        @php $activeOrders = $orders->whereNotIn('status', ['completed']); @endphp
        <div class="table-responsive">
            <table class="bw-table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Nama Layanan</th>
                        <th>Detail Jumlah</th>
                        <th>Total Biaya</th>
                        <th style="text-align: center;">Status</th>
                        <th>Tanggal Masuk</th>
                        <th>Estimasi Selesai</th>
                        <th style="width: 100px; text-align: center;">Cetak Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeOrders as $order)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary);">
                                {{ $order->order_code }}
                                @if($order->is_express)
                                    <span class="badge badge-express" style="font-size: 9px; padding: 2px 6px; margin-left: 4px;">EXPRESS</span>
                                @endif
                                @if($order->cashier_name === 'Online Order')
                                    <span class="badge" style="font-size: 9px; padding: 2px 6px; background: rgba(14,165,233,0.1); color: var(--primary); border: 1px solid rgba(14,165,233,0.2); margin-left: 2px;">WEB</span>
                                @endif
                            </td>
                            <td>{{ $order->service_name }}</td>
                            <td>
                                @if($order->detailOrders && $order->detailOrders->count() > 1)
                                    {{ $order->detailOrders->map(fn($d) => $d->service_type === 'kg' ? ($d->weight > 0 ? number_format($d->weight, 1).' Kg' : 'Timbangan') : $d->qty.' Pcs')->join(', ') }}
                                @elseif($order->service_type === 'kg')
                                    @if($order->weight == 0)
                                        <span style="font-size: 11px; font-style: italic; color: var(--text-muted);">Menunggu Timbangan</span>
                                    @else
                                        {{ number_format($order->weight, 1) }} Kg
                                    @endif
                                @else
                                    {{ $order->qty }} Pcs
                                @endif
                            </td>
                            <td style="font-weight: 700; color: var(--accent);">
                                @if($order->service_type === 'kg' && $order->weight == 0)
                                    <span style="font-size: 11px; font-style: italic; color: var(--text-muted);">Belum Dihitung</span>
                                @else
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="font-size: 10px; padding: 6px 12px; font-weight: 700; border-radius: 20px; text-transform: uppercase; display: inline-block;
                                    @if($order->status === 'pending') background: rgba(245,158,11,0.1); color: #d97706;
                                    @elseif($order->status === 'confirmed') background: rgba(14,165,233,0.1); color: #0284c7;
                                    @elseif($order->status === 'rejected') background: rgba(239,68,68,0.1); color: #ef4444;
                                    @elseif($order->status === 'processing') background: rgba(139,92,246,0.1); color: #7c3aed;
                                    @elseif($order->status === 'ready') background: var(--status-ready-bg); color: var(--status-ready);
                                    @elseif($order->status === 'completed') background: var(--status-completed-bg); color: var(--status-completed);
                                    @endif">
                                    @if($order->status === 'pending') Menunggu Konfirmasi
                                    @elseif($order->status === 'confirmed') Dikonfirmasi
                                    @elseif($order->status === 'rejected') Ditolak
                                    @elseif($order->status === 'processing') Sedang Diproses
                                    @elseif($order->status === 'ready')
                                        {{ $order->delivery_method === 'pickup_delivery' ? 'Siap Diantar' : 'Menunggu Diambil' }}
                                    @elseif($order->status === 'completed') Selesai
                                    @endif
                                </span>

                                {{-- Petunjuk status dikonfirmasi --}}
                                @if($order->status === 'confirmed')
                                    <div style="font-size: 11px; color: #0284c7; background: rgba(14,165,233,0.06); border: 1px solid rgba(14,165,233,0.15); border-radius: 8px; padding: 6px 10px; margin-top: 6px; line-height: 1.4; text-align: left; max-width: 220px; margin-left: auto; margin-right: auto;">
                                        @if($order->delivery_method === 'pickup_delivery')
                                            <i class="ri-motorbike-line"></i> Petugas akan menjemput cucian Anda sesuai jadwal.
                                        @else
                                            <i class="ri-store-2-line"></i> Silakan antar cucian Anda ke outlet kami.
                                        @endif
                                    </div>
                                @endif

                                {{-- Alasan penolakan --}}
                                @if($order->status === 'rejected' && $order->reject_reason)
                                    <div style="font-size: 11px; color: #ef4444; margin-top: 5px; font-weight: 600; line-height: 1.3; max-width: 200px; margin-left: auto; margin-right: auto;">
                                        <strong>Alasan:</strong> {{ $order->reject_reason }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $order->date_in->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $order->date_out->translatedFormat('d M Y H:i') }}</td>
                            <td>
                                <div class="action-buttons-cell" style="justify-content: center;">
                                    @if($order->status === 'rejected')
                                        <span style="font-size: 11px; color: var(--text-muted); font-style: italic;">—</span>
                                    @else
                                        <button type="button" onclick="printReceiptIframe({{ $order->id }})" class="btn-icon" title="Cetak Nota Resi">
                                            <i class="ri-printer-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-placeholder" style="padding: 40px 0;">
                                    <i class="ri-loader-3-line"></i>
                                    <p>Tidak ada pesanan aktif saat ini.</p>
                                    <a href="{{ route('customer.order') }}" class="btn-primary" style="margin-top: 10px; display: inline-flex; text-decoration: none; padding: 8px 16px; font-size: 12px;">
                                        <i class="ri-shirt-line"></i>
                                        <span>Pesan Laundry Sekarang</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab Content: Pesanan Selesai --}}
    <div id="panel-completed" class="tab-panel" style="display: none;">
        @php $completedOrders = $orders->where('status', 'completed'); @endphp
        <div class="table-responsive">
            <table class="bw-table">
                <thead>
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Nama Layanan</th>
                        <th>Detail Jumlah</th>
                        <th>Total Biaya</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Selesai</th>
                        <th style="width: 100px; text-align: center;">Cetak Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedOrders as $order)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary);">
                                {{ $order->order_code }}
                                @if($order->is_express)
                                    <span class="badge badge-express" style="font-size: 9px; padding: 2px 6px; margin-left: 4px;">EXPRESS</span>
                                @endif
                            </td>
                            <td>{{ $order->service_name }}</td>
                            <td>
                                @if($order->detailOrders && $order->detailOrders->count() > 1)
                                    {{ $order->detailOrders->map(fn($d) => $d->service_type === 'kg' ? number_format($d->weight, 1).' Kg' : $d->qty.' Pcs')->join(', ') }}
                                @elseif($order->service_type === 'kg')
                                    {{ number_format($order->weight, 1) }} Kg
                                @else
                                    {{ $order->qty }} Pcs
                                @endif
                            </td>
                            <td style="font-weight: 700; color: var(--accent);">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td>{{ $order->date_in->translatedFormat('d M Y H:i') }}</td>
                            <td>{{ $order->date_out->translatedFormat('d M Y H:i') }}</td>
                            <td>
                                <div class="action-buttons-cell" style="justify-content: center;">
                                    <button type="button" onclick="printReceiptIframe({{ $order->id }})" class="btn-icon" title="Cetak Nota Resi">
                                        <i class="ri-printer-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-placeholder" style="padding: 40px 0;">
                                    <i class="ri-checkbox-circle-line"></i>
                                    <p>Belum ada pesanan yang selesai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.history-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 8px 8px 0 0;
}
.history-tab:hover {
    color: var(--primary);
    background: rgba(14, 165, 233, 0.05);
}
.history-tab.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
    background: rgba(14, 165, 233, 0.06);
}
.tab-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(14, 165, 233, 0.12);
    color: var(--primary);
    min-width: 22px;
    text-align: center;
}
.tab-panel {
    padding-top: 10px;
}
</style>

@section('scripts')
<script>
    function switchTab(tab) {
        document.querySelectorAll('.history-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');

        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('panel-' + tab).style.display = 'block';
    }

    // If redirected after order submission, auto-switch to active tab
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        if (params.get('tab') === 'completed') {
            switchTab('completed');
        }
    });
</script>
@endsection
@endsection
