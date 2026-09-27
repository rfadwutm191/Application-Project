@extends('layouts.app')

@section('title', 'Pesan Laundry - Bluewash Laundry')
@section('header_title', 'Buat Pesanan Laundry')
@section('header_subtitle', 'Pesan penjemputan laundry langsung ke alamat Anda')

@section('content')
<div class="panel-card" style="width: 100%; max-width: 100%; margin-bottom: 30px;">
    <div class="panel-header" style="margin-bottom: 15px;">
        <h3 class="panel-title">
            <i class="ri-shirt-line"></i>
            <span>Formulir Pesanan Mandiri</span>
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

    <form action="{{ route('customer.order.store') }}" method="POST" class="auth-form">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
            
            {{-- Profil & Pengiriman --}}
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <h4 style="color: var(--primary); font-size: 14px; border-bottom: 1px solid rgba(15,23,42,0.08); padding-bottom: 5px; margin: 0;">
                    <i class="ri-user-line"></i> Data Profil & Pengiriman
                </h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <div class="input-container">
                            <i class="ri-user-smile-line input-icon"></i>
                            <input type="text" class="form-control" value="{{ $customer->name }}" disabled style="background: rgba(15, 23, 42, 0.03);">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nomor WhatsApp</label>
                        <div class="input-container">
                            <i class="ri-phone-line input-icon"></i>
                            <input type="text" class="form-control" value="{{ $customer->phone }}" disabled style="background: rgba(15, 23, 42, 0.03);">
                        </div>
                    </div>
                </div>

                {{-- Metode Layanan --}}
                <div class="form-group">
                    <label for="delivery_method">Pilih Metode Layanan</label>
                    <div class="input-container">
                        <i class="ri-route-line input-icon"></i>
                        <select id="deliverySelector" name="delivery_method" class="form-control" onchange="handleDeliveryChange()" style="appearance: none; -webkit-appearance: none; padding-right: 30px;" required>
                            <option value="drop_off">Drop Off (Antar Mandiri ke Outlet Toko)</option>
                            <option value="pickup_delivery">Pick Up & Delivery (Jemput & Antar ke Rumah)</option>
                        </select>
                        <i class="ri-arrow-down-s-line" style="position: absolute; right: 16px; pointer-events: none; color: var(--text-muted);"></i>
                    </div>
                </div>

                {{-- Alamat Pengantaran --}}
                <div id="addressGroup" class="form-group" style="display: none;">
                    <label for="delivery_address">Alamat Pengantaran / Penjemputan</label>
                    <div class="input-container">
                        <i class="ri-map-pin-line input-icon" style="top: 18px;"></i>
                        <textarea id="delivery_address" name="delivery_address" class="form-control" style="height: 80px; padding-left: 46px; resize: none;" placeholder="Masukkan alamat lengkap penjemputan...">{{ $customer->address }}</textarea>
                    </div>
                    <span style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">*Alamat jemput dapat disesuaikan khusus pesanan saat ini.</span>
                </div>
            </div>

            {{-- Detail Laundry & Harga --}}
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <h4 style="color: var(--primary); font-size: 14px; border-bottom: 1px solid rgba(15,23,42,0.08); padding-bottom: 5px; margin: 0;">
                    <i class="ri-shopping-bag-3-line"></i> Detail Laundry & Harga
                </h4>

                {{-- Grid Layanan & Tanggal --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="service_id">Jenis Layanan</label>
                        <div class="input-container">
                            <i class="ri-price-tag-3-line input-icon"></i>
                            <select id="serviceSelector" name="service_id" class="form-control" onchange="handleServiceChange()" style="appearance: none; -webkit-appearance: none; padding-right: 30px;" required>
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-type="{{ $service->type }}">
                                        {{ $service->name }} (Rp {{ number_format($service->price, 0, ',', '.') }}/{{ $service->type }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line" style="position: absolute; right: 16px; pointer-events: none; color: var(--text-muted);"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="order_date">Tanggal Pemesanan</label>
                        <div class="input-container">
                            <i class="ri-calendar-line input-icon"></i>
                            <input type="date" id="order_date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Input Spesifik Layanan --}}
                <div class="form-group" style="margin: 0;">
                    {{-- Qty Pcs --}}
                    <div id="qtyInputGroup" style="display: none;">
                        <label for="qtyInput">Jumlah Pakaian (Pcs)</label>
                        <div class="input-container" style="margin-top: 5px;">
                            <i class="ri-calculator-line input-icon"></i>
                            <input type="number" id="qtyInput" name="qty" class="form-control" min="1" placeholder="Jumlah pakaian..." value="1" oninput="calculateTotal()">
                        </div>
                    </div>

                    {{-- Informasi Kiloan --}}
                    <div id="weightNoticeGroup" style="display: none;">
                        <div style="background: rgba(14, 165, 233, 0.06); border: 1px solid rgba(14, 165, 233, 0.15); border-radius: 10px; padding: 10px 14px; font-size: 12.5px; color: var(--primary); display: flex; align-items: flex-start; gap: 8px; line-height: 1.35;">
                            <i class="ri-information-line" style="font-size: 18px; margin-top: 1px;"></i>
                            <span>Timbangan riil (Kg) diukur kasir di toko. Harga dihitung otomatis setelah ditimbang.</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Opsional --}}
                <div class="form-group">
                    <label for="notes">Catatan Tambahan (Opsional)</label>
                    <div class="input-container">
                        <i class="ri-sticky-note-line input-icon" style="top: 18px;"></i>
                        <textarea id="notes" name="notes" class="form-control" style="height: 60px; padding-left: 46px; resize: none;" placeholder="Contoh: Pisahkan baju putih..."></textarea>
                    </div>
                </div>

                {{-- Ringkasan Harga --}}
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; background: rgba(15,23,42,0.02); border: 1px solid rgba(15,23,42,0.06); border-radius: 12px; padding: 12px 16px; margin-top: 5px;">
                    {{-- Express --}}
                    <label class="form-check-group" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: var(--text-main); margin: 0;">
                        <input type="checkbox" id="expressCheckbox" name="is_express" value="1" onchange="calculateTotal()" style="width: 16px; height: 16px; accent-color: var(--primary);">
                        <span>Express (+Rp 10k, 1 Hari)</span>
                    </label>

                    {{-- Estimasi --}}
                    <div style="text-align: right; display: flex; flex-direction: column;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--text-muted);">Estimasi Total:</span>
                        <span id="calculatedPrice" style="font-size: 20px; font-weight: 800; color: var(--accent);">Rp 0</span>
                    </div>
                </div>

            </div>
            
        </div>

        {{-- Tombol Submit --}}
        <div style="display: flex; justify-content: center; margin-top: 25px; border-top: 1px solid rgba(15,23,42,0.06); padding-top: 20px;">
            <button type="submit" class="btn-primary" style="padding: 12px 40px; min-width: 420px;">
                <i class="ri-check-double-line"></i>
                <span>Kirim Pesanan Laundry</span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function handleServiceChange() {
        const serviceSelector = document.getElementById('serviceSelector');
        const selectedOption = serviceSelector.options[serviceSelector.selectedIndex];
        
        const qtyGroup = document.getElementById('qtyInputGroup');
        const weightNotice = document.getElementById('weightNoticeGroup');
        
        if (!selectedOption.value) {
            qtyGroup.style.display = 'none';
            weightNotice.style.display = 'none';
            document.getElementById('calculatedPrice').innerText = 'Rp 0';
            return;
        }

        const type = selectedOption.getAttribute('data-type');
        
        if (type === 'kg') {
            qtyGroup.style.display = 'none';
            weightNotice.style.display = 'block';
            document.getElementById('qtyInput').required = false;
        } else {
            qtyGroup.style.display = 'block';
            weightNotice.style.display = 'none';
            document.getElementById('qtyInput').required = true;
        }

        calculateTotal();
    }

    function handleDeliveryChange() {
        const deliverySelector = document.getElementById('deliverySelector');
        const addressGroup = document.getElementById('addressGroup');
        const addressInput = document.getElementById('delivery_address');

        if (deliverySelector.value === 'pickup_delivery') {
            addressGroup.style.display = 'block';
            addressInput.required = true;
        } else {
            addressGroup.style.display = 'none';
            addressInput.required = false;
        }
    }

    function calculateTotal() {
        const serviceSelector = document.getElementById('serviceSelector');
        const selectedOption = serviceSelector.options[serviceSelector.selectedIndex];
        const expressChecked = document.getElementById('expressCheckbox').checked;
        const priceLabel = document.getElementById('calculatedPrice');
        
        let total = 0;

        if (!selectedOption || !selectedOption.value) {
            priceLabel.innerText = 'Rp 0';
            return;
        }

        const price = parseInt(selectedOption.getAttribute('data-price')) || 0;
        const type = selectedOption.getAttribute('data-type');
        
        if (type === 'kg') {
            total = 0; 
        } else {
            const qty = parseInt(document.getElementById('qtyInput').value) || 1;
            total = price * qty;
        }

        if (expressChecked) {
            total += 10000;
        }

        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });

        if (type === 'kg') {
            priceLabel.innerHTML = expressChecked 
                ? '<span style="font-size: 11px; color: var(--text-muted); font-weight: normal;">Estimasi (+Exp):</span> ' + formatter.format(total).replace('IDR', 'Rp')
                : '<span style="font-size: 11px; color: var(--text-muted); font-weight: normal; font-style: italic;">Menunggu Timbangan</span>';
        } else {
            priceLabel.innerText = formatter.format(total).replace('IDR', 'Rp');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        handleServiceChange();
        handleDeliveryChange();
    });
</script>
@endsection
