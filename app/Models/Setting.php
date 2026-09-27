<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_laundry',
        'alamat_laundry',
        'no_telepon_laundry',
        'header_struk',
        'footer_struk',
        'wa_auto_send',
        'wa_device_token',
        'template_pesan_wa',
        'updated_by',
    ];

    protected $casts = [
        'wa_auto_send' => 'boolean',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function instance(): static
    {
        $setting = static::first();
        if (!$setting) {
            $setting = static::create([
                'nama_laundry' => 'Bluewash Laundry POS',
                'alamat_laundry' => 'Jl. Merdeka No. 45, Bandung',
                'no_telepon_laundry' => '081234567890',
                'header_struk' => 'Terima kasih telah mempercayakan pakaian Anda di Bluewash Laundry!',
                'footer_struk' => 'Garansi cuci ulang 1x24 jam setelah cucian diambil dengan membawa nota resmi.',
                'wa_auto_send' => true,
                'template_pesan_wa' => "Halo kak {nama}, cucian Anda dengan no. nota *{nota}* ({layanan}) di *{toko}* sudah SELESAI & SIAP DIAMBIL! Terima kasih!",
            ]);
        }
        return $setting;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::instance();

        $columnMap = [
            'store_name' => 'nama_laundry',
            'nama_laundry' => 'nama_laundry',
            'store_address' => 'alamat_laundry',
            'alamat_laundry' => 'alamat_laundry',
            'store_phone' => 'no_telepon_laundry',
            'no_telepon_laundry' => 'no_telepon_laundry',
            'receipt_header' => 'header_struk',
            'header_struk' => 'header_struk',
            'receipt_footer' => 'footer_struk',
            'footer_struk' => 'footer_struk',
            'wa_auto_send' => 'wa_auto_send',
            'wa_gateway_token' => 'wa_device_token',
            'wa_device_token' => 'wa_device_token',
            'wa_template' => 'template_pesan_wa',
            'template_pesan_wa' => 'template_pesan_wa',
        ];

        $col = $columnMap[$key] ?? $key;
        if (isset($setting->$col)) {
            $val = $setting->$col;
            if ($col === 'wa_auto_send') {
                return $val ? '1' : '0';
            }
            return (!is_null($val) && $val !== '') ? $val : $default;
        }

        return $default;
    }

    public static function set(string $key, mixed $value, ?int $userId = null): bool
    {
        $setting = static::instance();

        $columnMap = [
            'store_name' => 'nama_laundry',
            'nama_laundry' => 'nama_laundry',
            'store_address' => 'alamat_laundry',
            'alamat_laundry' => 'alamat_laundry',
            'store_phone' => 'no_telepon_laundry',
            'no_telepon_laundry' => 'no_telepon_laundry',
            'receipt_header' => 'header_struk',
            'header_struk' => 'header_struk',
            'receipt_footer' => 'footer_struk',
            'footer_struk' => 'footer_struk',
            'wa_auto_send' => 'wa_auto_send',
            'wa_gateway_token' => 'wa_device_token',
            'wa_device_token' => 'wa_device_token',
            'wa_template' => 'template_pesan_wa',
            'template_pesan_wa' => 'template_pesan_wa',
        ];

        $col = $columnMap[$key] ?? $key;

        if ($col === 'wa_auto_send') {
            $value = ($value === '1' || $value === 1 || $value === true || $value === 'on');
        }

        $setting->$col = $value;
        if ($userId) {
            $setting->updated_by = $userId;
        }

        return $setting->save();
    }
}
