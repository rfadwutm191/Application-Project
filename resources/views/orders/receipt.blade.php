@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->translatedFormat('d/m/Y H:i') : '-';
    $rp  = fn($n) => 'Rp ' . number_format((float)($n ?? 0), 0, ',', '.');

    $paid   = (int)($order->paid_amount ?? $order->total_price ?? 0);
    $change = (int)($order->change_amount ?? max(0, $paid - ($order->total_price ?? 0)));

    $store = [
        'name'    => \App\Models\Setting::get('store_name', 'BLUEWASH LAUNDRY'),
        'address' => \App\Models\Setting::get('store_address', 'Jl. Merdeka No. 45, Bandung'),
        'phone'   => \App\Models\Setting::get('store_phone', '081234567890'),
        'footer'  => \App\Models\Setting::get('footer_struk', "1. Pengambilan cucian wajib membawa nota resi ini.\n2. Kerusakan/luntur di luar tanggung jawab jika tidak dilaporkan sebelumnya.\n3. Garansi cuci berlaku 1 × 24 jam.")
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_code }} - Bluewash Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; color: #1e293b; font-family: 'Courier New', Courier, monospace; display: flex; justify-content: center; min-height: 100vh; padding: 40px 20px; }
        
        .receipt-actions { position: fixed; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 100; }
        .btn-action { background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 15px; font: 600 13px system-ui, -apple-system, sans-serif; color: #0f172a; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); text-decoration: none; transition: all 0.2s; }
        .btn-action:hover { background: #f8fafc; border-color: #94a3b8; transform: translateY(-1px); }
        .btn-print { background: #2563eb; border: none; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-wa { background: #25D366; color: #fff; border-color: #25D366; }

        .receipt-paper { background: #fff; width: 100%; max-width: 340px; padding: 24px 20px; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; font-size: 12px; line-height: 1.4; color: #0f172a; margin: 0 auto; }
        .receipt-header { text-align: center; margin-bottom: 14px; }
        .receipt-logo { max-width: 90px; max-height: 60px; object-fit: contain; display: block; margin: 0 auto 8px; }
        .receipt-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin: 4px 0 2px; }
        .receipt-subtitle { font-size: 10px; color: #64748b; margin: 2px 0; }
        .receipt-divider { border-bottom: 1px dashed #94a3b8; margin: 10px 0; }
        .receipt-row { display: flex; justify-content: space-between; align-items: flex-start; font-size: 11.5px; margin-bottom: 3px; }
        .receipt-item { margin-bottom: 8px; }
        .receipt-item-title { font-weight: 700; font-size: 12px; margin-bottom: 2px; }
        .receipt-item-details { display: flex; justify-content: space-between; font-size: 11px; color: #475569; }
        .receipt-total { font-size: 13px; font-weight: 800; padding-top: 4px; }
        .receipt-footer { text-align: center; margin-top: 16px; }

        .fw-bold { font-weight: bold; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-muted { color: #94a3b8; }
        .text-left { text-align: left; }
        .pre-line { white-space: pre-line; }

        @media print {
            @page { size: auto; margin: 0; }
            html, body { background: #fff !important; color: #000 !important; padding: 0 !important; margin: 0 !important; height: auto !important; display: block !important; position: static !important; }
            .receipt-actions { display: none !important; }
            .receipt-paper { display: block !important; position: static !important; border: none !important; box-shadow: none !important; margin: 0 auto !important; padding: 10px 12px !important; max-width: 80mm !important; width: 100% !important; border-radius: 0 !important; }
            .receipt-logo { max-width: 75px !important; margin: 0 auto 8px !important; }
            .receipt-row, .receipt-item-details { display: flex !important; justify-content: space-between !important; }
        }
    </style>
</head>
<body>

    <div class="receipt-actions">
        @auth
            <a href="{{ route('orders.index') }}" class="btn-action">
                <i class="ri-arrow-left-line"></i><span>Kembali ke Transaksi</span>
            </a>
        @else
            <a href="/" class="btn-action">
                <i class="ri-home-4-line"></i><span>Halaman Utama</span>
            </a>
        @endauth
        <a href="{{ $order->wa_url }}" target="_blank" class="btn-action btn-wa">
            <i class="ri-whatsapp-line"></i><span>Kirim WA</span>
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="ri-printer-line"></i><span>Cetak Resi</span>
        </button>
    </div>

    <div class="receipt-paper">
        <div class="receipt-header">
            <img src="{{ asset('assets/logo.png') }}" alt="Bluewash Logo" class="receipt-logo">
            <h1 class="receipt-title">{{ $store['name'] }}</h1>
            <p class="receipt-subtitle">Premium Laundry & Dry Cleaning</p>
            <p class="receipt-subtitle" style="font-size: 9px; margin-top: 4px;">{{ $store['address'] }}<br>Telp/WA: {{ $store['phone'] }}</p>
        </div>

        <div class="receipt-row"><span>No. Resi:</span><span class="fw-bold">{{ $order->order_code }}</span></div>
        <div class="receipt-row"><span>Tanggal:</span><span>{{ $fmt($order->date_in ?? $order->created_at) }}</span></div>
        <div class="receipt-row"><span>Kasir:</span><span>{{ $order->cashier_name ?? ($order->user->name ?? 'Kasir') }}</span></div>

        <div class="receipt-divider"></div>

        <div class="receipt-row"><span>Pelanggan:</span><span class="fw-bold">{{ $order->customer_name ?? 'Pelanggan POS' }}</span></div>
        <div class="receipt-row"><span>Telepon:</span><span>{{ $order->customer_phone ?? '-' }}</span></div>

        <div class="receipt-divider"></div>

        @forelse($order->detailOrders as $detail)
            <div class="receipt-item">
                <div class="receipt-item-title">{{ $detail->service_name }}</div>
                <div class="receipt-item-details">
                    <span>
                        @if($detail->service_type === 'kg')
                            {{ number_format((float)($detail->weight ?? 0), 1) }} Kg x {{ $rp($detail->price_per_unit) }}
                        @else
                            {{ $detail->qty ?? 1 }} Pcs x {{ $rp($detail->price_per_unit) }}
                        @endif
                        @if($detail->is_express) (Express) @endif
                    </span>
                    <span>{{ $rp($detail->subtotal) }}</span>
                </div>
            </div>
        @empty
            <div class="receipt-item">
                <div class="receipt-item-title">{{ $order->service_name ?? 'Layanan Laundry' }}</div>
                <div class="receipt-item-details">
                    <span>{{ number_format((float)($order->weight ?? 1), 1) }} Unit</span>
                    <span>{{ $rp($order->total_price) }}</span>
                </div>
            </div>
        @endforelse

        <div class="receipt-divider"></div>

        @if(($order->discount_amount ?? 0) > 0)
            <div class="receipt-row">
                <span>Diskon Pelanggan ({{ $order->discount ?? 5 }}%):</span>
                <span class="text-red">- {{ $rp($order->discount_amount) }}</span>
            </div>
        @endif

        <div class="receipt-row receipt-total">
            <span>TOTAL BIAYA:</span>
            <span>{{ $rp($order->total_price) }}</span>
        </div>

        <div class="receipt-row" style="margin-top: 5px; font-size: 11px;">
            <span>Bayar:</span>
            <span>{{ $rp($paid) }}</span>
        </div>
        <div class="receipt-row" style="font-size: 11px;">
            <span>Kembalian:</span>
            <span class="fw-bold text-green">{{ $change === 0 ? 'Pas' : $rp($change) }}</span>
        </div>

        <div class="receipt-divider" style="margin-top: 15px;"></div>

        <div class="receipt-row"><span>Estimasi Selesai:</span><span class="fw-bold">{{ $fmt($order->date_out) }}</span></div>
        @if($order->ready_at)
            <div class="receipt-row"><span>Tanggal Siap:</span><span>{{ $fmt($order->ready_at) }}</span></div>
        @endif
        @if($order->completed_at)
            <div class="receipt-row"><span>Tanggal Diambil:</span><span>{{ $fmt($order->completed_at) }}</span></div>
        @endif

        <div class="receipt-footer">
            <p class="fw-bold" style="margin-bottom: 5px;">Syarat & Ketentuan:</p>
            <p class="text-left pre-line" style="font-size: 8px; line-height: 1.4; margin-bottom: 12px;">{{ $store['footer'] }}</p>
            <p class="fw-bold" style="font-size: 10px; letter-spacing: 1px; margin-top: 15px;">*** TERIMA KASIH ***</p>
            <p class="text-muted" style="font-size: 8px; margin-top: 4px;">Senang melayani Anda</p>
        </div>
    </div>

</body>
</html>
