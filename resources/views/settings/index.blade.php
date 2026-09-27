@extends('layouts.app')

@php
    $currentTab = request('tab', 'store');
@endphp

@section('title', $currentTab === 'wa' ? 'Pengaturan WhatsApp Gateway - Bluewash Laundry' : 'Pengaturan Toko & Struk - Bluewash Laundry')
@section('header_title', $currentTab === 'wa' ? 'Pengaturan WhatsApp Gateway' : 'Pengaturan Toko & Struk')
@section('header_subtitle', $currentTab === 'wa' ? 'Konfigurasi Fonnte API Token dan kustomisasi pesan notifikasi otomatis' : 'Pengaturan nama toko, nomor telepon, alamat, dan format cetak resi')

@section('content')
<div style="width: 100%;">
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="tab" value="{{ $currentTab }}">

        @if($currentTab === 'wa')
            {{-- WhatsApp Gateway --}}
            <div style="background: white; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                    <div style="background: #f0fdf4; color: #16a34a; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold;">
                        <i class="ri-whatsapp-line"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 17px; color: #1e293b; font-weight: 700;">Konfigurasi Fonnte WhatsApp API Gateway</h3>
                        <p style="margin: 0; font-size: 13px; color: #64748b;">Koneksi API token dan kustomisasi template notifikasi otomatis</p>
                    </div>
                </div>

                <div class="settings-grid-2">
                    {{-- Token API --}}
                    <div>
                        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                            Koneksi API Gateway
                        </h4>

                        <div style="background: #f0fdf4; padding: 20px; border-radius: 14px; border: 1px solid #bbf7d0; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <label style="font-size: 13px; font-weight: 700; color: #166534; margin: 0; display: flex; align-items: center; gap: 6px;">
                                    <i class="ri-key-2-line"></i> Fonnte API Token *
                                </label>
                                <label style="font-size: 12px; color: #15803d; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; background: #dcfce7; padding: 6px 12px; border-radius: 20px; border: 1px solid #86efac;">
                                    <input type="checkbox" name="wa_auto_send" value="1" {{ old('wa_auto_send', $settings['wa_auto_send']) == '1' ? 'checked' : '' }}>
                                    Kirim WA Otomatis
                                </label>
                            </div>
                            <input type="password" name="wa_gateway_token" value="{{ old('wa_gateway_token', $settings['wa_gateway_token']) }}" placeholder="Masukkan Token Fonnte (misal: aBc123XyZ...)" class="form-control" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #86efac; font-size: 13px; font-family: monospace; background: white;">
                            <div style="margin-top: 10px; font-size: 12px; color: #166534; line-height: 1.4;">
                                <i class="ri-information-line"></i> Ambil token gratis di <a href="https://fonnte.com" target="_blank" style="color: #15803d; font-weight: 700; text-decoration: underline;">Fonnte.com</a> setelah scan QR.
                            </div>
                        </div>

                        {{-- Card Info --}}
                        <div style="background: #eff6ff; padding: 14px 16px; border-radius: 12px; border: 1px solid #bfdbfe; font-size: 12px; color: #1e40af; line-height: 1.5;">
                            <strong style="display: block; margin-bottom: 4px; font-weight: 700; font-size: 13px;">
                                <i class="ri-information-line"></i> Cara Kerja Notifikasi Otomatis:
                            </strong>
                            Pesan WA terkirim otomatis ke HP pelanggan saat status transaksi diubah menjadi <strong>Siap Diambil</strong>.
                        </div>
                    </div>

                    {{-- Template Pesan --}}
                    <div>
                        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                            Template Pesan Notifikasi
                        </h4>

                        <div style="margin-bottom: 16px;">
                            <label style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px;">
                                <span>Template Pesan WhatsApp *</span>
                                <span style="font-size: 11px; color: #16a34a; font-weight: 600;">Format Cucian Selesai</span>
                            </label>
                            <textarea name="wa_template" rows="6" class="form-control" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 13px; font-family: monospace; line-height: 1.5;" required>{{ old('wa_template', $settings['wa_template']) }}</textarea>
                        </div>


                    </div>
                </div>

                {{-- Hidden input toko --}}
                <input type="hidden" name="store_name" value="{{ $settings['store_name'] }}">
                <input type="hidden" name="store_phone" value="{{ $settings['store_phone'] }}">
                <input type="hidden" name="store_address" value="{{ $settings['store_address'] }}">
                <input type="hidden" name="receipt_header" value="{{ $settings['receipt_header'] }}">
                <input type="hidden" name="receipt_footer" value="{{ $settings['receipt_footer'] }}">

                <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn" style="background: #16a34a; color: white; padding: 10px 22px; border-radius: 8px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(22,163,74,0.2);">
                        <i class="ri-save-line" style="font-size: 16px;"></i> Simpan Pengaturan WhatsApp
                    </button>
                </div>
            </div>

        @else

            {{-- Pengaturan Toko --}}
            <div style="background: white; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                    <div style="background: #eff6ff; color: #2563eb; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold;">
                        <i class="ri-store-2-line"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 17px; color: #1e293b; font-weight: 700;">Informasi Toko / Outlet & Format Struk</h3>
                        <p style="margin: 0; font-size: 13px; color: #64748b;">Kelola nama laundry, kontak toko, alamat, dan catatan footer resi</p>
                    </div>
                </div>

                <div class="settings-grid-2">
                    {{-- Info Toko --}}
                    <div>
                        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                            Identitas Outlet / Toko
                        </h4>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Nama Laundry / Toko *</label>
                            <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" class="form-control" style="width: 100%; padding: 11px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">No. Telepon / WA Center *</label>
                            <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}" class="form-control" style="width: 100%; padding: 11px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Alamat Toko Lengkap *</label>
                            <textarea name="store_address" rows="4" class="form-control" style="width: 100%; padding: 11px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;" required>{{ old('store_address', $settings['store_address']) }}</textarea>
                        </div>
                    </div>

                    {{-- Format Struk --}}
                    <div>
                        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                            Format Cetak Struk Nota
                        </h4>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Header Struk Nota</label>
                            <input type="text" name="receipt_header" value="{{ old('receipt_header', $settings['receipt_header']) }}" class="form-control" style="width: 100%; padding: 11px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Footer / Catatan Bawah Struk</label>
                            <textarea name="receipt_footer" rows="5" class="form-control" style="width: 100%; padding: 11px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">{{ old('receipt_footer', $settings['receipt_footer']) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Hidden input WA --}}
                <input type="hidden" name="wa_template" value="{{ $settings['wa_template'] }}">
                <input type="hidden" name="wa_gateway_token" value="{{ $settings['wa_gateway_token'] }}">
                @if($settings['wa_auto_send'] == '1')
                    <input type="hidden" name="wa_auto_send" value="1">
                @endif

                <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn" style="background: #2563eb; color: white; padding: 10px 22px; border-radius: 8px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 3px 10px rgba(37,99,235,0.2);">
                        <i class="ri-save-line" style="font-size: 16px;"></i> Simpan Pengaturan Toko
                    </button>
                </div>
            </div>
        @endif
    </form>
</div>
@endsection
