@extends('layouts.app')

@section('title', 'Transaksi Laundry - Bluewash Laundry')
@section('header_title', 'Transaksi Laundry')
@section('header_subtitle', 'Kelola penerimaan cucian dan status transaksi')

@section('content')
<div class="table-actions">
    <form action="{{ route('orders.index') }}" method="GET" class="search-filter-form" style="display: flex; align-items: center; gap: 8px; margin: 0; flex: 1; min-width: 0;">
        <div class="search-box-wrapper" style="margin: 0; flex: 1; min-width: 130px;">
            <i class="ri-search-line"></i>
            <input type="text" name="search" class="search-control" placeholder="Cari Kode atau Pelanggan..." value="{{ $search }}">
        </div>

        <div style="flex-shrink: 0; max-width: 150px;">
            <select name="status" class="form-control" onchange="this.form.submit()" style="padding: 8px 10px; font-size: 13px; font-weight: 600; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #0f172a; cursor: pointer; text-overflow: ellipsis;">
                <option value="" {{ !$statusFilter ? 'selected' : '' }}>Semua Status</option>
                <option value="diproses" {{ $statusFilter === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="siap_diambil" {{ $statusFilter === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
    </form>
    
    <a href="{{ route('orders.create') }}" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; margin: 0; flex-shrink: 0;">
        <i class="ri-shopping-cart-2-line"></i>
        <span>Transaksi Baru</span>
    </a>
</div>

<div class="table-card">
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
                    <th style="width: 160px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
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
                            @if($order->detailOrders->count() > 1)
                                {{ $order->detailOrders->map(fn($d) => $d->service_type === 'kg' ? ($d->weight > 0 ? number_format($d->weight, 1).' Kg' : 'Timbangan') : $d->qty.' Pcs')->join(', ') }}
                            @elseif($order->service_type === 'kg')
                                @if($order->weight == 0)
                                    <span style="font-size: 11px; font-style: italic; color: #f59e0b; font-weight: 600;">Menunggu Timbangan</span>
                                @else
                                    {{ number_format($order->weight, 1) }} Kg
                                @endif
                            @else
                                {{ $order->qty }} Pcs
                            @endif
                        </td>
                        <td style="font-weight: 700; color: var(--text-main);">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td>
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('PATCH')
                                <div style="position: relative; display: inline-block;">
                                    <select name="status" class="form-control" onchange="this.form.submit()" 
                                            style="padding: 6px 20px 6px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; text-transform: uppercase; width: auto; cursor: pointer; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1;">
                                        <option value="diproses" {{ in_array($order->status, ['confirmed', 'queued', 'pending', 'diproses', 'processing']) ? 'selected' : '' }}>Diproses</option>
                                        <option value="siap_diambil" {{ in_array($order->status, ['ready', 'siap_diambil']) ? 'selected' : '' }}>Siap Diambil</option>
                                        <option value="selesai" {{ in_array($order->status, ['completed', 'selesai']) ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                            </form>
                        </td>
                        <td>{{ optional($order->date_in)->translatedFormat('d M Y H:i') ?? optional($order->created_at)->translatedFormat('d M Y H:i') }}</td>
                        <td style="color: var(--text-main);">
                            {{ optional($order->date_out)->translatedFormat('d M Y H:i') ?? '-' }}
                        </td>
                        <td>
                            <div class="action-buttons-cell">
                                <button type="button" class="btn-icon view" 
                                    data-code="{{ e($order->order_code) }}"
                                    data-name="{{ e($order->customer_name) }}"
                                    data-phone="{{ e($order->customer_phone) }}"
                                    data-service="{{ e($order->service_name) }}"
                                    data-type="{{ e($order->service_type) }}"
                                    data-qty="{{ e($order->service_type === 'kg' ? ($order->weight > 0 ? number_format($order->weight, 1).' Kg' : 'Menunggu Timbangan') : $order->qty.' Pcs') }}"
                                    data-price="{{ e('Rp '.number_format($order->total_price, 0, ',', '.')) }}"
                                    data-items="{{ e(json_encode($order->detailOrders->map(function($d) { return ['name' => $d->service_name, 'type' => $d->service_type, 'weight' => (float)$d->weight, 'qty' => (int)$d->qty, 'price' => (int)$d->price_per_unit, 'is_express' => (bool)$d->is_express, 'subtotal' => (int)$d->subtotal]; }))) }}"
                                    data-express="{{ $order->is_express ? 'true' : 'false' }}"
                                    data-status="{{ e($order->status) }}"
                                    data-datein="{{ e(optional($order->date_in)->translatedFormat('d M Y H:i') ?? '-') }}"
                                    data-dateout="{{ e(optional($order->date_out)->translatedFormat('d M Y H:i') ?? '-') }}"
                                    data-delivery="{{ e($order->delivery_method) }}"
                                    data-address="{{ e($order->delivery_address ?? '') }}"
                                    data-notes="{{ e($order->notes ?? '') }}"
                                    data-cashier="{{ e($order->cashier_name ?? 'Kasir') }}"
                                    data-waurl="{{ e($order->wa_url) }}"
                                    onclick="openDetailModal(this)"
                                    title="Lihat Detail Transaksi">
                                    <i class="ri-eye-line"></i>
                                </button>

                                <button type="button" class="btn-icon edit" 
                                    onclick="openEditModal({{ $order->id }}, '{{ e($order->order_code) }}', '{{ e($order->service_type) }}', {{ $order->weight }}, {{ $order->qty }}, '{{ e($order->status) }}', '{{ e(addslashes($order->customer_name)) }}', '{{ e(addslashes($order->service_name)) }}', {{ $order->price_per_unit }}, {{ $order->is_express ? 'true' : 'false' }})" 
                                    title="Edit Transaksi POS">
                                    <i class="ri-pencil-line"></i>
                                </button>

                                <button type="button" onclick="printReceiptIframe({{ $order->id }})" class="btn-icon print" title="Cetak Nota Thermal">
                                    <i class="ri-printer-line"></i>
                                </button>
                                
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini secara permanen?')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Hapus Transaksi">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Edit Transaksi --}}
<div id="editWeightModal" class="modal-overlay">
    <div class="modal-container" style="max-width: 460px; border-radius: 16px; padding: 24px;">
        <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="editModalTitle" style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">Edit Transaksi</h3>
            <button class="btn-close-modal" onclick="closeModal('editWeightModal')" style="border: none; background: transparent; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="editWeightForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">

                {{-- Ringkasan Info Transaksi --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #64748b; font-weight: 600;">Pelanggan:</span>
                        <strong id="editCustomerName" style="color: #0f172a;">-</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: #64748b; font-weight: 600;">Layanan:</span>
                        <strong id="editServiceName" style="color: #2563eb;">-</strong>
                    </div>
                </div>

                {{-- Status Cucian --}}
                <div class="form-group" style="margin: 0;">
                    <label for="edit_status" style="font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">Status Cucian POS</label>
                    <div class="input-container">
                        <i class="ri-radar-line input-icon"></i>
                        <select id="edit_status" name="status" class="form-control" style="padding-left: 46px; font-weight: 600; border-radius: 10px;">
                            <option value="diproses">Sedang Diproses</option>
                            <option value="siap_diambil">Siap Diambil (Kirim WA)</option>
                            <option value="selesai">Selesai / Sudah Diambil</option>
                        </select>
                    </div>
                </div>

                {{-- Weight Input (Kg) --}}
                <div id="editWeightGroup" class="form-group" style="margin: 0; display: none;">
                    <label for="edit_weight" style="font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">Hasil Timbangan (Kg)</label>
                    <div class="input-container">
                        <i class="ri-scales-line input-icon"></i>
                        <input type="number" id="edit_weight" name="weight" class="form-control" step="0.1" min="0.1" placeholder="Contoh: 3.5" style="padding-left: 46px; border-radius: 10px;" oninput="calculateEditTotal()">
                    </div>
                </div>

                {{-- Qty Input (Pcs) --}}
                <div id="editQtyGroup" class="form-group" style="margin: 0; display: none;">
                    <label for="edit_qty" style="font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">Jumlah Item (Pcs)</label>
                    <div class="input-container">
                        <i class="ri-calculator-line input-icon"></i>
                        <input type="number" id="edit_qty" name="qty" class="form-control" min="1" placeholder="1" style="padding-left: 46px; border-radius: 10px;" oninput="calculateEditTotal()">
                    </div>
                </div>

                {{-- Estimasi Total Biaya Terbaru --}}
                <div style="background: #eff6ff; border: 1px dashed #3b82f6; border-radius: 12px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; font-weight: 700; color: #1e40af;">Kalkulasi Total Biaya:</span>
                    <span id="editCalculatedTotal" style="font-size: 16px; font-weight: 800; color: #1d4ed8;">Rp 0</span>
                </div>

            </div>
            <div class="modal-footer" style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn-secondary" onclick="closeModal('editWeightModal')">Batal</button>
                <button type="submit" class="btn-primary" style="background: #2563eb; color: #ffffff; font-weight: 700; border-radius: 10px; padding: 10px 20px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-save-line"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Detail Transaksi --}}
<div id="detailOrderModal" class="modal-overlay">
    <div class="modal-container" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="detailModalTitle">Detail Transaksi (BW-XXXX)</h3>
            <button class="btn-close-modal" onclick="closeModal('detailOrderModal')">&times;</button>
        </div>
        <div class="modal-body" style="padding-top: 10px;">
            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 13px;">
                
                {{-- Status & Date --}}
                <div style="background: rgba(15,23,42,0.03); padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(15,23,42,0.05);">
                    <div>
                        <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Status Cucian</span>
                        <div id="detailStatusBadge" style="margin-top: 4px;">—</div>
                    </div>
                </div>

                {{-- Info Pelanggan --}}
                <div style="border-bottom: 1px solid rgba(15,23,42,0.06); padding-bottom: 12px; margin-top: 4px;">
                    <div style="font-weight: 700; color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Informasi Pelanggan</div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 4px 0; color: var(--text-muted); width: 140px;">Nama Pelanggan</td>
                            <td style="padding: 4px 0; font-weight: 700;" id="detailCustomerName">—</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; color: var(--text-muted);">No. WhatsApp</td>
                            <td style="padding: 4px 0; font-weight: 700;" id="detailCustomerPhone">—</td>
                        </tr>
                        <tr id="detailAddressRow" style="display: none;">
                            <td style="padding: 4px 0; color: var(--text-muted); vertical-align: top;">Alamat Jemput/Antar</td>
                            <td style="padding: 4px 0; font-weight: 700; line-height: 1.3;" id="detailCustomerAddress">—</td>
                        </tr>
                    </table>
                </div>

                {{-- Detail Cucian & Biaya --}}
                <div style="border-bottom: 1px solid rgba(15,23,42,0.06); padding-bottom: 12px;">
                    <div style="font-weight: 700; color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Detail Cucian & Biaya</div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 1px dashed rgba(15,23,42,0.12); color: var(--text-muted); font-size: 11px; text-transform: uppercase;">
                                <th style="text-align: left; padding: 4px 0; font-weight: 700;">Layanan</th>
                                <th style="text-align: center; padding: 4px 0; font-weight: 700;">Jumlah / Berat</th>
                                <th style="text-align: right; padding: 4px 0; font-weight: 700;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody">
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 1px dashed rgba(15,23,42,0.15);">
                                <td colspan="2" style="padding: 8px 0 0 0; font-weight: 800; color: var(--text-main);">Total Biaya</td>
                                <td style="padding: 8px 0 0 0; font-weight: 800; color: var(--accent); font-size: 15px; text-align: right;" id="detailTotalPrice">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Logistik & Catatan --}}
                <div>
                    <div style="font-weight: 700; color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Logistik & Catatan</div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 4px 0; color: var(--text-muted); width: 140px;">Tanggal Masuk</td>
                            <td style="padding: 4px 0; font-weight: 700;" id="detailDateIn">—</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; color: var(--text-muted);">Estimasi Selesai</td>
                            <td style="padding: 4px 0; font-weight: 700;" id="detailDateOut">—</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; color: var(--text-muted);">Petugas Penerima</td>
                            <td style="padding: 4px 0; font-weight: 700;" id="detailCashierName">—</td>
                        </tr>
                        <tr id="detailNotesRow" style="display: none;">
                            <td style="padding: 4px 0; color: #b45309; vertical-align: top;">Catatan Tambahan</td>
                            <td style="padding: 4px 0; font-weight: 700; color: #b45309; font-style: italic; line-height: 1.3;" id="detailNotes">—</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid rgba(15,23,42,0.06); padding-top: 15px; display: flex; justify-content: space-between; gap: 10px;">
            <a id="detailWaBtn" href="#" target="_blank" class="btn" style="background: #25D366; color: white; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px;">
                <i class="ri-whatsapp-line" style="font-size: 16px;"></i> Kirim Notifikasi WA
            </a>
            <button type="button" class="btn-primary" onclick="closeModal('detailOrderModal')">Tutup Detail</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    let currentEditPricePerUnit = 0;
    let currentEditServiceType = 'kg';
    let currentEditIsExpress = false;

    function openEditModal(orderId, orderCode, serviceType, currentWeight, currentQty, currentStatus, customerName, serviceName, pricePerUnit, isExpress) {
        const form = document.getElementById('editWeightForm');
        form.action = `/orders/${orderId}`;

        document.getElementById('editModalTitle').innerText = 'Edit Transaksi \u2013 ' + orderCode;
        document.getElementById('editCustomerName').innerText = customerName || '-';
        document.getElementById('editServiceName').innerText = serviceName || '-';

        currentEditPricePerUnit = pricePerUnit || 0;
        currentEditServiceType = serviceType;
        currentEditIsExpress = isExpress || false;

        document.getElementById('edit_status').value = (currentStatus === 'ready') ? 'siap_diambil' : ((currentStatus === 'completed') ? 'selesai' : 'diproses');

        const weightGroup = document.getElementById('editWeightGroup');
        const qtyGroup    = document.getElementById('editQtyGroup');
        const weightInput = document.getElementById('edit_weight');
        const qtyInput    = document.getElementById('edit_qty');

        if (serviceType === 'kg') {
            weightGroup.style.display = 'block';
            qtyGroup.style.display    = 'none';
            weightInput.value         = currentWeight > 0 ? currentWeight : '';
            weightInput.required      = true;
            qtyInput.required         = false;
        } else {
            weightGroup.style.display = 'none';
            qtyGroup.style.display    = 'block';
            qtyInput.value            = currentQty;
            qtyInput.required         = true;
            weightInput.required      = false;
        }

        calculateEditTotal();
        document.getElementById('editWeightModal').classList.add('active');
    }

    function calculateEditTotal() {
        let total = 0;
        if (currentEditServiceType === 'kg') {
            const w = parseFloat(document.getElementById('edit_weight').value) || 0;
            total = w * currentEditPricePerUnit;
        } else {
            const q = parseInt(document.getElementById('edit_qty').value) || 0;
            total = q * currentEditPricePerUnit;
        }

        if (currentEditIsExpress) {
            total += 10000;
        }

        document.getElementById('editCalculatedTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(total));
    }

    function openDetailModal(el) {
        try {
            const orderCode = el.getAttribute('data-code');
            const customerName = el.getAttribute('data-name');
            const customerPhone = el.getAttribute('data-phone');
            const totalPrice = el.getAttribute('data-price');
            const status = el.getAttribute('data-status');
            const dateIn = el.getAttribute('data-datein');
            const dateOut = el.getAttribute('data-dateout');
            const deliveryMethod = el.getAttribute('data-delivery');
            const deliveryAddress = el.getAttribute('data-address');
            const notes = el.getAttribute('data-notes');
            const cashierName = el.getAttribute('data-cashier');
            const waUrl = el.getAttribute('data-waurl');
            const rawItems = el.getAttribute('data-items');

            document.getElementById('detailWaBtn').href = waUrl || '#';

            document.getElementById('detailModalTitle').innerText = 'Detail Transaksi – ' + orderCode;
            document.getElementById('detailCustomerName').innerText = customerName;
            document.getElementById('detailCustomerPhone').innerText = customerPhone;
            document.getElementById('detailTotalPrice').innerText = totalPrice;
            document.getElementById('detailDateIn').innerText = dateIn;
            document.getElementById('detailDateOut').innerText = dateOut;
            document.getElementById('detailCashierName').innerText = cashierName;

            // Rincian item
            const itemsBody = document.getElementById('detailItemsBody');
            if (itemsBody) {
                itemsBody.innerHTML = '';
                let items = [];
                try {
                    items = JSON.parse(rawItems || '[]');
                } catch(err) {
                    items = [];
                }

                if (items && items.length > 0) {
                    items.forEach(item => {
                        const qtyText = item.type === 'kg' 
                            ? (item.weight > 0 ? (parseFloat(item.weight).toFixed(1) + ' Kg') : 'Menunggu Timbangan')
                            : (item.qty + ' Pcs');
                        const subtotalFmt = 'Rp ' + new Intl.NumberFormat('id-ID').format(item.subtotal);
                        const expressBadge = item.is_express ? ' <span style="font-size: 9px; padding: 1px 5px; background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 4px; font-weight: 700;">EXPRESS</span>' : '';

                        const tr = document.createElement('tr');
                        tr.style.borderBottom = '1px solid rgba(15,23,42,0.04)';
                        tr.innerHTML = `
                            <td style="padding: 6px 0; font-weight: 600; color: #1e293b;">${item.name}${expressBadge}</td>
                            <td style="padding: 6px 0; text-align: center; font-weight: 600; color: #475569;">${qtyText}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #0f172a;">${subtotalFmt}</td>
                        `;
                        itemsBody.appendChild(tr);
                    });
                } else {
                    const serviceName = el.getAttribute('data-service') || '-';
                    const serviceQty = el.getAttribute('data-qty') || '-';
                    itemsBody.innerHTML = `
                        <tr>
                            <td style="padding: 6px 0; font-weight: 600; color: #1e293b;">${serviceName}</td>
                            <td style="padding: 6px 0; text-align: center; font-weight: 600; color: #475569;">${serviceQty}</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #0f172a;">${totalPrice}</td>
                        </tr>
                    `;
                }
            }

            let statusBadge = document.getElementById('detailStatusBadge');
            let statusText = status;
            let statusStyle = 'font-size: 10px; padding: 4px 10px; font-weight: 700; border-radius: 20px; text-transform: uppercase; display: inline-block;';
            
            if (in_array(status, ['diproses', 'processing', 'confirmed', 'queued', 'pending', 'antrean'])) {
                statusStyle += 'background: rgba(14,165,233,0.12); color: #0284c7;';
                statusText = 'Diproses';
            } else if (in_array(status, ['siap_diambil', 'ready'])) {
                statusStyle += 'background: rgba(16,185,129,0.12); color: #10b981;';
                statusText = 'Siap Diambil';
            } else if (in_array(status, ['selesai', 'completed'])) {
                statusStyle += 'background: rgba(5,150,105,0.15); color: #059669;';
                statusText = 'Selesai';
            }
            statusBadge.innerHTML = `<span style="${statusStyle}">${statusText}</span>`;

            let deliveryBadge = document.getElementById('detailDeliveryBadge');
            if (deliveryBadge) {
                deliveryBadge.innerHTML = `<span style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase;">${deliveryMethod === 'pickup_delivery' ? 'Antar Jemput' : 'Drop Off'}</span>`;
            }

            if (deliveryAddress && deliveryAddress !== 'null') {
                document.getElementById('detailAddressRow').style.display = 'table-row';
                document.getElementById('detailCustomerAddress').innerText = deliveryAddress;
            } else {
                document.getElementById('detailAddressRow').style.display = 'none';
            }

            if (notes && notes !== 'null') {
                document.getElementById('detailNotesRow').style.display = 'table-row';
                document.getElementById('detailNotes').innerText = notes;
            } else {
                document.getElementById('detailNotesRow').style.display = 'none';
            }

            document.getElementById('detailOrderModal').classList.add('active');
        } catch (e) {
            console.error(e);
        }
    }

    function in_array(needle, haystack) {
        return haystack.includes(needle);
    }
</script>
@endsection
