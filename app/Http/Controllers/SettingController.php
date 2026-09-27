<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::instance();

        $settings = [
            'store_name' => $setting->nama_laundry,
            'store_address' => $setting->alamat_laundry,
            'store_phone' => $setting->no_telepon_laundry,
            'receipt_header' => $setting->header_struk,
            'receipt_footer' => $setting->footer_struk,
            'wa_template' => $setting->template_pesan_wa,
            'wa_gateway_token' => $setting->wa_device_token ?: env('FONNTE_TOKEN', ''),
            'wa_auto_send' => $setting->wa_auto_send ? '1' : '0',
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:50',
            'receipt_header' => 'nullable|string|max:255',
            'receipt_footer' => 'nullable|string',
            'wa_template' => 'required|string',
            'wa_gateway_token' => 'nullable|string|max:255',
            'wa_auto_send' => 'nullable|in:0,1,on',
        ]);

        $setting = Setting::instance();
        $setting->update([
            'nama_laundry' => $request->store_name,
            'alamat_laundry' => $request->store_address,
            'no_telepon_laundry' => $request->store_phone,
            'header_struk' => $request->receipt_header,
            'footer_struk' => $request->receipt_footer,
            'template_pesan_wa' => $request->wa_template,
            'wa_device_token' => $request->wa_gateway_token,
            'wa_auto_send' => $request->has('wa_auto_send'),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengaturan toko dan Fonnte WhatsApp Gateway berhasil disimpan!');
    }
}
