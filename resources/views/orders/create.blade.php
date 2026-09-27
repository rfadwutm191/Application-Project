@extends('layouts.app')

@section('title', 'Kasir / Point of Sale - Bluewash Laundry')
@section('header_title', 'Kasir / Point of Sale')
@section('header_subtitle', 'Pilih layanan laundry, tentukan pelanggan, dan proses transaksi POS')

@section('header_actions')
    <a href="{{ route('orders.index') }}" class="btn-secondary" style="font-size: 12px; font-weight: 600; text-decoration: none; padding: 7px 14px; border-radius: 8px; display: inline-flex; align-items: center;">
        Kembali ke Daftar
    </a>
@endsection

@section('content')
<div style="width: 100%; margin: 0; padding: 0;">

    {{-- Layout POS --}}
    <form action="{{ route('orders.store') }}" method="POST" id="posOrderForm" style="width: 100%;">
        @csrf
        
        <div class="pos-layout-grid">
            
            {{-- Katalog Layanan --}}
            <div style="background: #ffffff; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-main); min-width: 0; width: 100%; display: flex; flex-direction: column;">
                
                <div style="margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Pilih Layanan Laundry</h3>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">Klik kartu produk / layanan untuk menambahkan ke keranjang</p>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 18px;">
                    <input type="text" id="searchServiceInput" class="form-control" placeholder="Cari nama layanan..." style="flex: 1; min-width: 0; padding: 9.5px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" onkeyup="filterServices()">
                    <select id="filterTypeSelect" class="form-control" style="width: 120px; padding: 9.5px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 500; flex-shrink: 0; background: #ffffff;" onchange="filterServices()">
                        <option value="">Semua</option>
                        <option value="kg">Kiloan (KG)</option>
                        <option value="pcs">Satuan (PCS)</option>
                    </select>
                </div>

                <div id="serviceGrid" class="pos-service-grid">
                    @foreach($services as $service)
                        <div class="service-card" data-name="{{ strtolower($service->name) }}" data-type="{{ $service->type }}" 
                             onclick="addToCart({{ $service->id }}, '{{ e(addslashes($service->name)) }}', {{ $service->price }}, '{{ $service->type }}')"
                             style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; cursor: pointer; transition: var(--transition); display: flex; flex-direction: column; justify-content: space-between; min-height: 100px;"
                             onmouseover="this.style.borderColor='#2563eb'; this.style.boxShadow='0 4px 14px rgba(37,99,235,0.12)'; this.style.transform='translateY(-2px)';"
                             onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.transform='none';">
                            
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 8px;">
                                    <div style="font-size: 13.5px; font-weight: 700; color: #0f172a; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        {{ $service->name }}
                                    </div>
                                    <span style="font-size: 10px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 2px 7px; border-radius: 5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em; flex-shrink: 0;">
                                        {{ $service->type }}
                                    </span>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 8px; border-top: 1px dashed #e2e8f0;">
                                <div style="font-size: 13.5px; font-weight: 800; color: #2563eb;">
                                    Rp {{ number_format($service->price, 0, ',', '.') }}
                                </div>
                                <span style="width: 28px; height: 28px; background: #2563eb; color: #ffffff; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; transition: var(--transition);">
                                    <i class="ri-add-line" style="font-size: 18px; font-weight: bold;"></i>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Keranjang Belanja Panel --}}
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-main); overflow: hidden; display: flex; flex-direction: column; position: sticky; top: 20px;">
                
                <div style="background: #2563eb; color: #ffffff; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 14px; font-weight: 700;">
                        <span>Keranjang Belanja</span>
                    </div>
                    <span id="cartCountBadge" style="background: rgba(255,255,255,0.25); font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 6px;">
                        0 item
                    </span>
                </div>

                <div style="padding: 14px; min-height: 210px; max-height: 380px; overflow-y: auto; border-bottom: 1px solid #f1f5f9;">
                    <div id="cartItemsContainer">
                        <div id="emptyCartPlaceholder" style="text-align: center; padding: 36px 12px; color: #64748b;">
                            <p style="margin: 0; font-size: 12.5px; color: #64748b; font-weight: 500;">Klik produk / layanan di sebelah kiri untuk menambah ke keranjang</p>
                        </div>
                    </div>
                </div>

                <div style="padding: 12px 14px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 10px;">
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="font-size: 12px; font-weight: 700; color: #b45309;">
                            Express 1 Hari (+Rp 10.000)
                        </div>
                        <input type="checkbox" name="is_express" id="is_express" value="1" onchange="calculateCartTotal()" style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                    </div>

                    <div>
                        <input type="text" name="notes" id="notes" placeholder="Catatan khusus cucian (opsional)..." class="form-control" style="width: 100%; padding: 8px 11px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 12.5px; background: #ffffff;">
                    </div>
                </div>

                {{-- Cart Footer --}}
                <div style="padding: 16px; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                        <span style="font-size: 14px; font-weight: 700; color: #0f172a;">Total Biaya</span>
                        <span id="cartTotalDisplay" style="font-size: 22px; font-weight: 800; color: #2563eb;">Rp 0</span>
                    </div>

                    {{-- Hidden Inputs --}}
                    <input type="hidden" name="customer_id" id="form_customer_id" value="">
                    <input type="hidden" name="new_customer_name" id="form_new_customer_name" value="">
                    <input type="hidden" name="new_customer_phone" id="form_new_customer_phone" value="">
                    <input type="hidden" name="new_customer_address" id="form_new_customer_address" value="">

                    <input type="hidden" name="items" id="form_items_json" value="">
                    <input type="hidden" name="service_id" id="form_service_id" value="">
                    <input type="hidden" name="service_type" id="form_service_type" value="kg">
                    <input type="hidden" name="weight" id="form_weight" value="1">
                    <input type="hidden" name="qty" id="form_qty" value="1">
                    <input type="hidden" name="discount" id="form_discount" value="0">
                    <input type="hidden" name="discount_amount" id="form_discount_amount" value="0">
                    <input type="hidden" name="paid_amount" id="form_paid_amount" value="">
                    <input type="hidden" name="print_after_save" id="print_after_save" value="0">

                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="clearCart()" class="btn-secondary" style="flex: 1; justify-content: center; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                            Batal
                        </button>
                        <button type="button" onclick="openPaymentModal()" class="btn-primary" style="flex: 1.6; background: #2563eb; border-color: #1d4ed8; justify-content: center; padding: 10px; border-radius: 8px; font-size: 14px; font-weight: 700;">
                            Bayar
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

{{-- Modal Pembayaran --}}
<div id="paymentModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; justify-content: center; align-items: center; padding: 16px; opacity: 1; pointer-events: auto;">
    <div style="background: #ffffff; width: 100%; max-width: 440px; border-radius: 14px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25); overflow: hidden; border: 1px solid #e2e8f0;">
        
        {{-- Modal Header --}}
        <div style="padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 18px;">📄</span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">Proses Pembayaran</h3>
            </div>
            <button type="button" onclick="closePaymentModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #64748b; line-height: 1;">&times;</button>
        </div>

        <div style="padding: 18px; max-height: 82vh; overflow-y: auto;">
            
            {{-- Ringkasan Item --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                <div id="modalSummaryItems" style="font-size: 13px; color: #334155; display: flex; flex-direction: column; gap: 6px;">
                    {{-- Rendered dynamically by JS --}}
                </div>
                
                <div style="border-top: 1px solid #cbd5e1; margin-top: 12px; padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">Subtotal</span>
                    <span id="modalSubtotalDisplay" style="font-size: 13px; font-weight: 600; color: #334155;">Rp 0</span>
                </div>

                <div id="modalDiscountRow" style="display: none; justify-content: space-between; align-items: center; margin-top: 4px;">
                    <span id="modalDiscountLabel" style="font-size: 13px; font-weight: 700; color: #16a34a;">Diskon Member 5%</span>
                    <span id="modalDiscountDisplay" style="font-size: 13px; font-weight: 700; color: #16a34a;">- Rp 0</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                    <span style="font-size: 15px; font-weight: 800; color: #0f172a;">Total Bayar</span>
                    <span id="modalTotalDisplay" style="font-size: 19px; font-weight: 800; color: #2563eb;">Rp 0</span>
                </div>
            </div>

            {{-- Form Data Pelanggan --}}
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 6px; margin: 0;">
                        Data Pelanggan
                    </label>
                    <button type="button" id="btnUbahCustomer" onclick="resetModalCustomerSearch()" style="background: none; border: none; font-size: 12.5px; font-weight: 600; color: #2563eb; cursor: pointer; display: none; text-decoration: underline;">
                        Ubah
                    </button>
                </div>

                {{-- Input No HP & Tombol Cek --}}
                <div id="modalCustomerSearchContainer">
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="modalCustomerSearchInput" class="form-control" placeholder="Masukkan no. HP pelanggan..." style="flex: 1; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" onkeypress="if(event.key==='Enter'){ event.preventDefault(); checkCustomerInModal(); }">
                        <button type="button" onclick="checkCustomerInModal()" class="btn-primary" style="padding: 9px 16px; font-size: 13px; border-radius: 8px; font-weight: 700; background: #2563eb;">
                            Cek
                        </button>
                    </div>

                </div>

                {{-- Pelanggan Ditemukan Card --}}
                <div id="memberFoundCard" style="display: none; background: #e6f4ea; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; margin-top: 6px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #16a34a;">
                            Pelanggan Ditemukan
                        </div>
                        <span id="memberFoundTierBadge" style="background: #22c55e; color: #ffffff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 12px; display: none;">
                            Pelanggan
                        </span>
                    </div>
                    <div id="memberFoundName" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">-</div>
                    <div id="memberFoundPhone" style="font-size: 12px; color: #64748b; margin-top: 1px;">-</div>
                    <div id="memberFoundAddressContainer" style="font-size: 11.5px; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                        <span id="memberFoundAddress">-</span>
                    </div>
                </div>

                {{-- Form Pelanggan Baru --}}
                <div id="modalNewCustomerForm" style="display: none; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 10px 12px; margin-top: 8px;">
                    <div style="font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; margin-bottom: 6px;">
                        + Form Pelanggan Baru
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <input type="text" id="modal_new_name" placeholder="Masukkan Nama Lengkap..." class="form-control" style="width: 100%; padding: 8px 10px; border-radius: 6px; font-size: 12.5px;">
                        <input type="text" id="modal_new_phone" placeholder="No. WhatsApp..." class="form-control" style="width: 100%; padding: 8px 10px; border-radius: 6px; font-size: 12.5px;">
                        <input type="text" id="modal_new_address" placeholder="Alamat..." class="form-control" style="width: 100%; padding: 8px 10px; border-radius: 6px; font-size: 12.5px;">
                    </div>
                </div>

            </div>

            {{-- Form Nominal Bayar --}}
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Nominal Bayar</label>
                <select id="paidAmountSelect" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid #0f172a; font-size: 14px; font-weight: 700; color: #0f172a; background: #ffffff; cursor: pointer;" onchange="handlePaidSelectChange(this.value)">
                    {{-- Populated dynamically by JS --}}
                </select>

                <div id="customPaidInputWrapper" style="display: none; margin-top: 8px;">
                    <input type="text" id="paidAmountInput" class="form-control" placeholder="Masukkan nominal lainnya..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 14px; font-weight: 700; color: #0f172a;" oninput="handlePaidInput(this.value)">
                </div>

                {{-- Display Kembalian --}}
                <div style="margin-top: 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 12.5px; font-weight: 700; color: #475569; display: flex; align-items: center;">
                        Kembalian
                    </div>
                    <span id="changeDisplay" style="font-size: 16px; font-weight: 800; color: #16a34a;">Rp 0</span>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closePaymentModal()" style="flex: 1; background: #ffffff; border: 1.5px solid #cbd5e1; color: #334155; padding: 11px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="button" onclick="submitPOSOrder()" style="flex: 1; background: #16a34a; border: none; color: #ffffff; padding: 11px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3); transition: var(--transition);" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                    Bayar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="receiptModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 10000; justify-content: center; align-items: center; padding: 16px; opacity: 1; pointer-events: auto;">
    <div style="background: #ffffff; width: 100%; max-width: 400px; border-radius: 12px; box-shadow: var(--shadow-lg); overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <div>
                <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">Struk Pembayaran</h3>
                <p style="margin: 1px 0 0 0; font-size: 11.5px; color: #64748b;">Transaksi berhasil diproses.</p>
            </div>
            <button onclick="closeReceiptModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>

        {{-- Struk Content --}}
        <div style="padding: 16px; max-height: 55vh; overflow-y: auto; background: #f8fafc;">
            <div id="receiptPaper" style="background: #ffffff; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: 'Courier New', Courier, monospace; font-size: 12.5px; color: #1e293b;">
                {{-- Rendered by JS --}}
            </div>
        </div>

        {{-- Tombol Struk --}}
        <div style="padding: 14px 18px; border-top: 1px solid #e2e8f0; background: #ffffff; display: flex; gap: 12px; justify-content: center;">
            <button type="button" onclick="printReceipt()" class="btn-primary" style="flex: 1; justify-content: center; padding: 10px 18px; border-radius: 12px; font-weight: 700; background: #2563eb; border: none; font-size: 13.5px; display: inline-flex; align-items: center;">
                Cetak Struk
            </button>
            <button type="button" onclick="saveWithoutPrint()" class="btn-secondary" style="flex: 1; justify-content: center; padding: 10px 18px; border-radius: 12px; font-weight: 600; background: #ffffff; border: 1.5px solid #64748b; color: #1e293b; font-size: 13.5px;">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const customersList = @json($customers);
    let cart = [];
    let currentTotal = 0;
    let currentFinalTotal = 0;
    let selectedCustomerId = '';
    let selectedCustomerName = '';
    let selectedCustomerPhone = '';
    let selectedCustomerAddress = '-';
    let selectedCustomerPoints = 0;

    // Filter service catalog
    function filterServices() {
        const query = document.getElementById('searchServiceInput').value.toLowerCase();
        const typeFilter = document.getElementById('filterTypeSelect').value;
        const cards = document.querySelectorAll('.service-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const type = card.getAttribute('data-type');

            const matchesQuery = name.includes(query);
            const matchesType = typeFilter === '' || type === typeFilter;

            if (matchesQuery && matchesType) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add item to shopping cart
    function addToCart(id, name, price, type) {
        let existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id: id, name: name, price: price, type: type, qty: 1 });
        }
        renderCart();

        if (type === 'kg') {
            setTimeout(() => {
                const inp = document.getElementById(`weight-input-${id}`);
                if (inp) {
                    inp.focus();
                    inp.select();
                }
            }, 60);
        }
    }

    function updateQty(id, delta) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                removeFromCart(id);
                return;
            }
        }
        renderCart();
    }

    function updateWeight(id, val) {
        let weight = parseFloat(val);
        let item = cart.find(i => i.id === id);
        if (item) {
            if (isNaN(weight) || weight < 0) weight = 0;
            item.qty = weight;
        }

        const subtotalEl = document.getElementById(`subtotal-${id}`);
        if (subtotalEl && item) {
            const subtotal = Math.round(item.price * item.qty);
            subtotalEl.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        }

        if (cart.length > 0 && cart[0].id === id) {
            document.getElementById('form_weight').value = item.qty;
        }

        calculateCartTotal();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const badge = document.getElementById('cartCountBadge');

        if (cart.length === 0) {
            container.innerHTML = `
                <div id="emptyCartPlaceholder" style="text-align: center; padding: 36px 12px; color: #64748b;">
                    <p style="margin: 0; font-size: 12.5px; color: #64748b; font-weight: 500;">Klik produk / layanan di sebelah kiri untuk menambah ke keranjang</p>
                </div>
            `;
            badge.innerText = '0 item';
            document.getElementById('form_service_id').value = '';
            calculateCartTotal();
            return;
        }

        let totalItems = cart.length;
        badge.innerText = `${totalItems} item`;

        let html = '';
        cart.forEach(item => {
            const subtotal = Math.round(item.price * item.qty);
            const isKg = (item.type === 'kg');

            let qtyControlsHtml = '';
            if (isKg) {
                qtyControlsHtml = `
                    <div style="display: flex; align-items: center; gap: 4px; justify-content: flex-end;">
                        <span style="font-size: 11px; font-weight: 700; color: #475569;">Berat:</span>
                        <input type="number" step="0.1" min="0.1" id="weight-input-${item.id}" value="${item.qty}" 
                               oninput="updateWeight(${item.id}, this.value)" 
                               style="width: 58px; padding: 3px 4px; font-size: 12px; font-weight: 800; border: 1.5px solid #2563eb; border-radius: 6px; text-align: center; color: #0f172a; background: #fff;" 
                               placeholder="1.0">
                        <span style="font-size: 11.5px; font-weight: 700; color: #0f172a;">Kg</span>
                        <button type="button" onclick="removeFromCart(${item.id})" style="background: none; border: none; color: #dc2626; font-size: 11px; font-weight: 600; margin-left: 4px; cursor: pointer;">Hapus</button>
                    </div>
                `;
            } else {
                qtyControlsHtml = `
                    <div style="display: flex; align-items: center; gap: 5px; justify-content: flex-end;">
                        <button type="button" onclick="updateQty(${item.id}, -1)" style="width: 22px; height: 22px; border-radius: 5px; border: 1px solid #cbd5e1; background: white; font-weight: bold; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">-</button>
                        <span style="font-size: 12px; font-weight: 700; min-width: 16px; text-align: center;">${item.qty} Pcs</span>
                        <button type="button" onclick="updateQty(${item.id}, 1)" style="width: 22px; height: 22px; border-radius: 5px; border: 1px solid #cbd5e1; background: white; font-weight: bold; cursor: pointer; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">+</button>
                        <button type="button" onclick="removeFromCart(${item.id})" style="background: none; border: none; color: #dc2626; font-size: 11px; font-weight: 600; margin-left: 4px; cursor: pointer;">Hapus</button>
                    </div>
                `;
            }

            html += `
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="flex: 1; min-width: 0; padding-right: 8px;">
                        <div style="font-size: 13px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</div>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 1px;">
                            Rp ${item.price.toLocaleString('id-ID')} / ${item.type}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div id="subtotal-${item.id}" style="font-size: 13px; font-weight: 800; color: #2563eb; margin-bottom: 4px;">
                            Rp ${subtotal.toLocaleString('id-ID')}
                        </div>
                        ${qtyControlsHtml}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        if (cart.length > 0) {
            document.getElementById('form_service_id').value = cart[0].id;
            document.getElementById('form_service_type').value = cart[0].type;
            if (cart[0].type === 'kg') {
                document.getElementById('form_weight').value = cart[0].qty;
                document.getElementById('form_qty').value = 1;
            } else {
                document.getElementById('form_qty').value = cart[0].qty;
                document.getElementById('form_weight').value = 0;
            }
        }

        calculateCartTotal();
    }

    function calculateCartTotal() {
        let subtotal = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
        const expressEl = document.getElementById('is_express');
        const isExpress = expressEl ? expressEl.checked : false;

        if (isExpress && cart.length > 0) {
            subtotal += 10000;
        }

        currentTotal = Math.round(subtotal);

        let discountPercent = 0;
        if (selectedCustomerId || selectedCustomerPhone) {
            discountPercent = 5;
        }
        let discountAmount = Math.round(currentTotal * (discountPercent / 100));
        let finalTotal = Math.max(0, currentTotal - discountAmount);

        const formDiscount = document.getElementById('form_discount');
        if (formDiscount) formDiscount.value = discountPercent;

        const formDiscountAmount = document.getElementById('form_discount_amount');
        if (formDiscountAmount) formDiscountAmount.value = discountAmount;

        const totalDisplay = document.getElementById('cartTotalDisplay');
        if (totalDisplay) {
            totalDisplay.innerText = 'Rp ' + finalTotal.toLocaleString('id-ID');
        }

        return { subtotal: currentTotal, discountPercent, discountAmount, finalTotal };
    }

    // Open Payment Modal
    function openPaymentModal() {
        if (cart.length === 0) {
            alert('Keranjang belanja masih kosong! Silakan klik "+ Tambah" pada salah satu layanan terlebih dahulu.');
            return;
        }

        const totals = calculateCartTotal();

        let summaryHtml = '';
        cart.forEach(item => {
            const sub = item.price * item.qty;
            summaryHtml += `
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 500; color: #334155;">
                    <span>${item.name} x${item.qty}</span>
                    <span>Rp ${sub.toLocaleString('id-ID')}</span>
                </div>
            `;
        });

        const expressEl = document.getElementById('is_express');
        if (expressEl && expressEl.checked) {
            summaryHtml += `
                <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: #b45309; margin-top: 2px;">
                    <span>Surcharge Express (1 Hari)</span>
                    <span style="font-weight: 600;">Rp 10.000</span>
                </div>
            `;
        }

        const summaryItems = document.getElementById('modalSummaryItems');
        if (summaryItems) summaryItems.innerHTML = summaryHtml;

        const subtotalDisplay = document.getElementById('modalSubtotalDisplay');
        if (subtotalDisplay) subtotalDisplay.innerText = 'Rp ' + totals.subtotal.toLocaleString('id-ID');

        const discountRow = document.getElementById('modalDiscountRow');
        const discountDisplay = document.getElementById('modalDiscountDisplay');
        const discountLabel = document.getElementById('modalDiscountLabel');
        if (totals.discountPercent > 0) {
            if (discountRow) discountRow.style.display = 'flex';
            if (discountLabel) discountLabel.innerText = `Diskon Member ${totals.discountPercent}%`;
            if (discountDisplay) discountDisplay.innerText = `- Rp ${totals.discountAmount.toLocaleString('id-ID')}`;
        } else {
            if (discountRow) discountRow.style.display = 'none';
        }

        const totalDisplay = document.getElementById('modalTotalDisplay');
        if (totalDisplay) totalDisplay.innerText = 'Rp ' + totals.finalTotal.toLocaleString('id-ID');

        currentFinalTotal = totals.finalTotal;
        generateQuickButtons(totals.finalTotal);
        setPaidAmount(totals.finalTotal);

        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
        }

        setTimeout(() => {
            const searchInp = document.getElementById('modalCustomerSearchInput');
            if (searchInp && searchInp.offsetParent !== null) searchInp.focus();
        }, 100);
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
        }
    }

    function resetModalCustomerSearch() {
        selectedCustomerId = '';
        selectedCustomerName = '';
        selectedCustomerPhone = '';
        selectedCustomerAddress = '-';
        selectedCustomerPoints = 0;

        document.getElementById('modalCustomerSearchContainer').style.display = 'block';
        document.getElementById('memberFoundCard').style.display = 'none';
        document.getElementById('modalNewCustomerForm').style.display = 'none';
        document.getElementById('btnUbahCustomer').style.display = 'none';

        const custSelect = document.getElementById('modalCustomerSelect');
        if (custSelect) custSelect.value = '';
        document.getElementById('modalCustomerSearchInput').value = '';

        document.getElementById('form_customer_id').value = '';
        document.getElementById('form_new_customer_name').value = '';
        document.getElementById('form_new_customer_phone').value = '';

        openPaymentModal();
    }

    // Customer Lookup inside Modal
    function checkCustomerInModal() {
        const query = document.getElementById('modalCustomerSearchInput').value.trim().toLowerCase();

        if (!query) {
            alert('Silakan masukkan nomor HP atau nama pelanggan terlebih dahulu!');
            const inp = document.getElementById('modalCustomerSearchInput');
            if (inp) inp.focus();
            return;
        }

        const cleanQuery = query.replace(/[^0-9a-zA-Z]/g, '');

        const matchedCustomer = customersList.find(c => {
            const isAktif = (c.status || 'aktif').toLowerCase() === 'aktif';
            if (!isAktif) return false;

            const rawPhone = (c.telepon || c.phone || '').toString().toLowerCase();
            const rawName = (c.nama || c.name || '').toString().toLowerCase();
            const cleanPhone = rawPhone.replace(/[^0-9]/g, '');

            const matchPhoneDirect = rawPhone && rawPhone.includes(query);
            const matchPhoneClean = cleanPhone && cleanQuery && cleanPhone.includes(cleanQuery);
            const matchName = rawName && rawName.includes(query);

            return matchPhoneDirect || matchPhoneClean || matchName;
        });

        if (matchedCustomer) {
            applyCustomerData(matchedCustomer);
        } else {
            selectedCustomerId = '';
            document.getElementById('modalCustomerSearchContainer').style.display = 'none';
            document.getElementById('memberFoundCard').style.display = 'none';
            document.getElementById('modalNewCustomerForm').style.display = 'block';
            document.getElementById('btnUbahCustomer').style.display = 'inline-block';

            document.getElementById('modal_new_phone').value = query;
            if (document.getElementById('modal_new_name')) {
                document.getElementById('modal_new_name').focus();
            }
        }
    }

    function applyCustomerData(cust) {
        selectedCustomerId = cust.id;
        selectedCustomerName = cust.nama || cust.name || 'Pelanggan';
        selectedCustomerPhone = cust.telepon || cust.phone || '-';
        selectedCustomerAddress = cust.alamat || cust.address || '-';
        selectedCustomerPoints = 0;

        document.getElementById('memberFoundName').innerText = selectedCustomerName;
        document.getElementById('memberFoundPhone').innerText = selectedCustomerPhone;
        document.getElementById('memberFoundAddress').innerText = selectedCustomerAddress;

        document.getElementById('modalCustomerSearchContainer').style.display = 'none';
        document.getElementById('memberFoundCard').style.display = 'block';
        document.getElementById('modalNewCustomerForm').style.display = 'none';
        document.getElementById('btnUbahCustomer').style.display = 'inline-block';

        document.getElementById('form_customer_id').value = selectedCustomerId;
        document.getElementById('form_new_customer_name').value = '';
        document.getElementById('form_new_customer_phone').value = '';

        openPaymentModal();
    }

    function getPresetsList(total) {
        if (total <= 0) return [10000, 20000, 50000, 100000];

        const notes = [5000, 10000, 15000, 20000, 50000, 100000];
        const rounded = [
            total,
            Math.ceil(total / 5000) * 5000,
            Math.ceil(total / 10000) * 10000,
            Math.ceil(total / 20000) * 20000,
            Math.ceil(total / 50000) * 50000,
            100000,
            ...notes
        ];

        return Array.from(new Set(rounded.filter(v => v >= total && (v <= 100000 || v === total)))).sort((a, b) => a - b);
    }

    function generateQuickButtons(total) {
        const select = document.getElementById('paidAmountSelect');
        if (!select) return;

        const presets = getPresetsList(total);
        let html = '';

        presets.forEach((amt) => {
            const isPas = (amt === total);
            const label = isPas 
                ? `Rp ${amt.toLocaleString('id-ID')} (Uang Pas)` 
                : `Rp ${amt.toLocaleString('id-ID')}`;
            html += `<option value="${amt}">${label}</option>`;
        });

        html += `<option value="custom">Nominal Lainnya (Input Manual)...</option>`;
        select.innerHTML = html;
        select.value = total;

        const customWrapper = document.getElementById('customPaidInputWrapper');
        if (customWrapper) customWrapper.style.display = 'none';

        calculateChange(total);
    }

    function handlePaidSelectChange(val) {
        const customWrapper = document.getElementById('customPaidInputWrapper');
        if (val === 'custom') {
            if (customWrapper) customWrapper.style.display = 'block';
            const customInput = document.getElementById('paidAmountInput');
            if (customInput) {
                customInput.value = '';
                customInput.focus();
            }
            calculateChange(0);
        } else {
            if (customWrapper) customWrapper.style.display = 'none';
            const amt = parseInt(val) || 0;
            calculateChange(amt);
        }
    }

    function setPaidAmount(amt) {
        const select = document.getElementById('paidAmountSelect');
        if (select) {
            select.value = amt;
            handlePaidSelectChange(amt);
        }
    }

    function handlePaidInput(rawVal) {
        const numOnly = rawVal.replace(/\D/g, '');
        const amount = parseInt(numOnly) || 0;
        const input = document.getElementById('paidAmountInput');
        if (input) input.value = amount > 0 ? 'Rp ' + amount.toLocaleString('id-ID') : '';
        calculateChange(amount);
    }

    function calculateChange(paid) {
        const change = Math.max(0, paid - currentFinalTotal);
        const changeEl = document.getElementById('changeDisplay');
        if (changeEl) {
            changeEl.innerText = 'Rp ' + change.toLocaleString('id-ID');
        }
    }

    let activeSavedOrderId = null;

    async function submitPOSOrder() {
        if (cart.length === 0) {
            alert('Keranjang belanja masih kosong! Silakan tambah layanan terlebih dahulu.');
            return;
        }

        const select = document.getElementById('modalCustomerSelect');
        const searchInp = document.getElementById('modalCustomerSearchInput');
        const newNameInp = document.getElementById('modal_new_name');
        const newPhoneInp = document.getElementById('modal_new_phone');

        if (selectedCustomerId) {
            document.getElementById('form_customer_id').value = selectedCustomerId;
            document.getElementById('form_new_customer_name').value = '';
            document.getElementById('form_new_customer_phone').value = '';
        } else if (select && select.value) {
            document.getElementById('form_customer_id').value = select.value;
            const opt = select.options[select.selectedIndex];
            selectedCustomerName = opt.getAttribute('data-name');
            selectedCustomerPhone = opt.getAttribute('data-phone');
        } else {
            let phoneVal = (newPhoneInp && newPhoneInp.value.trim()) ? newPhoneInp.value.trim() : (searchInp ? searchInp.value.trim() : '');

            // Peringatan pop up di atas jika nomor HP belum diisi
            if (!phoneVal || phoneVal === '08000000000' || phoneVal === '-') {
                alert('Silakan masukkan nomor HP / WhatsApp pelanggan terlebih dahulu!');
                if (searchInp) {
                    searchInp.focus();
                } else if (newPhoneInp) {
                    newPhoneInp.focus();
                }
                return;
            }

            let nameVal = (newNameInp && newNameInp.value.trim()) ? newNameInp.value.trim() : 'Pelanggan POS';

            document.getElementById('form_customer_id').value = '';
            document.getElementById('form_new_customer_name').value = nameVal;
            document.getElementById('form_new_customer_phone').value = phoneVal;
            selectedCustomerName = nameVal;
            selectedCustomerPhone = phoneVal;
        }

        // Populate items json hidden input
        const itemsJsonInp = document.getElementById('form_items_json');
        if (itemsJsonInp) {
            itemsJsonInp.value = JSON.stringify(cart);
        }

        const paidSelect = document.getElementById('paidAmountSelect');
        let paid = currentFinalTotal;

        if (paidSelect) {
            if (paidSelect.value === 'custom') {
                const paidText = (document.getElementById('paidAmountInput')?.value || '').replace(/\D/g, '');
                paid = parseInt(paidText) || currentFinalTotal;
            } else {
                paid = parseInt(paidSelect.value) || currentFinalTotal;
            }
        }

        const formPaid = document.getElementById('form_paid_amount');
        if (formPaid) formPaid.value = paid;

        closePaymentModal();

        const form = document.getElementById('posOrderForm');
        const formData = new FormData(form);

        try {
            const response = await fetch("{{ route('orders.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                activeSavedOrderId = data.order_id;
                showReceiptModal(data.order_code, paid);
            } else {
                const errData = await response.json().catch(() => null);
                if (errData && errData.message) {
                    alert('Gagal menyimpan transaksi: ' + errData.message);
                } else {
                    form.submit();
                }
            }
        } catch (e) {
            console.error(e);
            form.submit();
        }
    }

    function showReceiptModal(savedCode, paidVal) {
        const paid = paidVal || currentFinalTotal;
        const change = Math.max(0, paid - currentFinalTotal);

        const now = new Date();
        const dateStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + ' ' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
        const orderCode = savedCode || ('BW-' + Math.floor(1000 + Math.random() * 9000));

        let itemsHtml = '';
        cart.forEach(item => {
            const sub = item.price * item.qty;
            itemsHtml += `
                <div style="display: flex; justify-content: space-between; font-size: 11.5px; margin-bottom: 2px;">
                    <span>${item.name} x${item.qty}</span>
                    <span>Rp ${sub.toLocaleString('id-ID')}</span>
                </div>
            `;
        });

        let receiptHtml = `
            <div style="text-align: center; margin-bottom: 12px;">
                <img src="{{ asset('assets/logo.png') }}" alt="Bluewash Logo" style="max-width: 70px; max-height: 50px; width: auto; height: auto; display: block; margin: 0 auto 6px auto; object-fit: contain;">
                <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px; color: #0f172a;">BLUEWASH LAUNDRY</div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">Struk Pembelian<br>Kasir: {{ optional(Auth::user())->name ?? 'Kasir' }}</div>
            </div>
            
            <div style="border-bottom: 1px dashed #94a3b8; margin: 8px 0;"></div>
            
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 2px;">
                <span>No. Order</span>
                <span style="font-weight: bold;">${orderCode}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 2px;">
                <span>Tanggal</span>
                <span>${dateStr}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 2px;">
                <span>Pelanggan</span>
                <span>${selectedCustomerName || 'Pelanggan POS'}</span>
            </div>
            
            <div style="border-bottom: 1px dashed #94a3b8; margin: 8px 0;"></div>
            
            ${itemsHtml}
            
            <div style="border-bottom: 1px dashed #94a3b8; margin: 8px 0;"></div>
            
            <div style="display: flex; justify-content: space-between; font-size: 12.5px; font-weight: bold; margin-bottom: 2px;">
                <span>TOTAL</span>
                <span>Rp ${currentFinalTotal.toLocaleString('id-ID')}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #475569; margin-top: 2px;">
                <span>Bayar</span>
                <span>Rp ${paid.toLocaleString('id-ID')}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #16a34a; font-weight: bold; margin-top: 2px;">
                <span>Kembalian</span>
                <span>${change === 0 ? 'Pas' : 'Rp ' + change.toLocaleString('id-ID')}</span>
            </div>

            <div style="border-bottom: 1px dashed #94a3b8; margin: 12px 0 8px 0;"></div>
            
            <div style="text-align: center; font-size: 10.5px; color: #64748b;">
                Terima kasih telah mencuci di Bluewash!
            </div>
        `;

        document.getElementById('receiptPaper').innerHTML = receiptHtml;

        const modal = document.getElementById('receiptModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
        }
    }

    function printReceipt() {
        if (activeSavedOrderId) {
            printReceiptIframe(activeSavedOrderId);
        } else {
            const inp = document.getElementById('print_after_save');
            if (inp) inp.value = '1';
            document.getElementById('posOrderForm').submit();
        }
    }

    function saveWithoutPrint() {
        closeReceiptModal();
    }

    function closeReceiptModal() {
        const modal = document.getElementById('receiptModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
        }
        activeSavedOrderId = null;
        clearCart();
    }
</script>
@endsection
