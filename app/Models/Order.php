<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_nota',
        'customer_id',
        'user_id',
        'status',
        'discount',
        'discount_amount',
        'total_biaya',
        'estimasi_selesai',
        'tanggal_siap',
        'tanggal_diambil',
        'catatan',
    ];

    protected $casts = [
        'estimasi_selesai' => 'datetime',
        'tanggal_siap' => 'datetime',
        'tanggal_diambil' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class);
    }

    public function logNotifikasiWa()
    {
        return $this->hasMany(LogNotifikasiWa::class);
    }

    // Pelanggan & Kasir
    public function getNamaPelangganAttribute()
    {
        return $this->customer ? $this->customer->nama : 'Pelanggan POS';
    }

    public function getTeleponPelangganAttribute()
    {
        return $this->customer ? $this->customer->telepon : '-';
    }

    public function getNamaKasirAttribute()
    {
        return $this->user ? $this->user->name : 'Kasir Utama';
    }

    // Alias atribut
    public function getOrderCodeAttribute() { return $this->attributes['kode_nota'] ?? ''; }
    public function setOrderCodeAttribute($value) { $this->attributes['kode_nota'] = $value; }

    public function getCustomerNameAttribute() { return $this->nama_pelanggan; }
    public function getCustomerPhoneAttribute() { return $this->telepon_pelanggan; }
    public function getCashierNameAttribute() { return $this->nama_kasir; }

    public function getDiscountAttribute() { return $this->attributes['discount'] ?? 0; }
    public function setDiscountAttribute($value) { $this->attributes['discount'] = $value; }

    public function getDiscountPercentAttribute() { return $this->discount; }
    public function getPersenDiskonAttribute() { return $this->discount; }
    public function setPersenDiskonAttribute($value) { $this->attributes['discount'] = $value; }

    public function getDiscountAmountAttribute() { return $this->attributes['discount_amount'] ?? 0; }
    public function setDiscountAmountAttribute($value) { $this->attributes['discount_amount'] = $value; }

    public function getJumlahDiskonAttribute() { return $this->discount_amount; }
    public function setJumlahDiskonAttribute($value) { $this->attributes['discount_amount'] = $value; }

    public function getTotalPriceAttribute() { return $this->attributes['total_biaya'] ?? 0; }
    public function setTotalPriceAttribute($value) { $this->attributes['total_biaya'] = $value; }

    public function getUangBayarAttribute() { return $this->attributes['total_biaya'] ?? 0; }
    public function getPaidAmountAttribute() { return $this->total_biaya; }

    public function getKembalianAttribute() { return 0; }
    public function getChangeAmountAttribute() { return 0; }

    public function getTanggalMasukAttribute() { return $this->created_at; }
    public function getDateInAttribute() { return $this->created_at; }
    public function setDateInAttribute($value) { }

    public function getDateOutAttribute() { return $this->estimasi_selesai; }
    public function setDateOutAttribute($value) { $this->attributes['estimasi_selesai'] = $value; }

    public function getReadyAtAttribute() { return $this->tanggal_siap; }
    public function setReadyAtAttribute($value) { $this->attributes['tanggal_siap'] = $value; }

    public function getTanggalSelesaiAttribute() { return $this->tanggal_diambil; }
    public function setTanggalSelesaiAttribute($value) { $this->attributes['tanggal_diambil'] = $value; }
    public function getCompletedAtAttribute() { return $this->tanggal_diambil; }
    public function setCompletedAtAttribute($value) { $this->attributes['tanggal_diambil'] = $value; }

    public function getNotesAttribute() { return $this->attributes['catatan'] ?? ''; }
    public function setNotesAttribute($value) { $this->attributes['catatan'] = $value; }

    public function getOrderDateAttribute()
    {
        return $this->created_at;
    }

    public function getIsExpressAttribute(): bool
    {
        $detail = $this->detailOrders->first();
        return $detail ? (bool)$detail->is_express : false;
    }

    public function getServiceNameAttribute(): string
    {
        $details = $this->detailOrders;
        if ($details->isEmpty()) {
            return 'Layanan Laundry';
        }
        if ($details->count() === 1) {
            return $details->first()->service_name;
        }
        return $details->pluck('service_name')->implode(', ');
    }

    public function getServiceTypeAttribute(): string
    {
        $detail = $this->detailOrders->first();
        return $detail ? $detail->service_type : 'kg';
    }

    public function getWeightAttribute(): float
    {
        $detail = $this->detailOrders->first();
        return $detail ? (float)$detail->weight : 0.0;
    }

    public function getQtyAttribute(): int
    {
        $detail = $this->detailOrders->first();
        return $detail ? (int)$detail->qty : 1;
    }

    public function getPricePerUnitAttribute(): int
    {
        $detail = $this->detailOrders->first();
        return $detail ? (int)$detail->price_per_unit : 0;
    }

    public function hitungTotal(): int
    {
        $subtotalSum = $this->detailOrders()->sum('subtotal');
        $finalTotal = max(0, $subtotalSum - ($this->discount_amount ?? 0));
        $this->total_biaya = $finalTotal;
        $this->save();

        return $finalTotal;
    }

    public function updateStatus(string $newStatus): void
    {
        $this->status = $newStatus;

        if (($newStatus === 'ready' || $newStatus === 'siap_diambil') && !$this->tanggal_siap) {
            $this->tanggal_siap = now();
        }

        if (($newStatus === 'completed' || $newStatus === 'selesai') && !$this->tanggal_diambil) {
            $this->tanggal_diambil = now();
            if (!$this->tanggal_siap) {
                $this->tanggal_siap = now();
            }
        }

        $this->save();
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->telepon_pelanggan ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    public function getWaUrlAttribute(): string
    {
        $storeName = Setting::get('store_name', 'Bluewash Laundry');
        $defaultTemplate = "Halo kak {nama}, cucian Anda dengan no. nota *{nota}* ({layanan}) di *{toko}* sudah SELESAI & SIAP DIAMBIL! Terima kasih!";
        
        $template = Setting::get('wa_template');
        
        $details = $this->detailOrders;
        if ($details && $details->count() > 1) {
            $serviceDetails = $details->map(function($d) {
                $unit = $d->service_type === 'kg' ? number_format((float)($d->weight ?? 0), 1).' Kg' : ($d->qty ?? 1).' Pcs';
                return $d->service_name . ' (' . $unit . ')';
            })->implode(', ');
        } else {
            $serviceDetails = $this->service_name;
        }

        $message = str_replace(
            ['{nama}', '{nota}', '{layanan}', '{toko}', '{total}'],
            [
                $this->nama_pelanggan ?? 'Pelanggan',
                $this->kode_nota ?? '',
                $serviceDetails ?: 'Layanan Laundry',
                $storeName ?? 'Bluewash Laundry',
                number_format((float)($this->total_biaya ?? 0), 0, ',', '.')
            ],
            $template ?: $defaultTemplate
        );

        return 'https://wa.me/' . $this->formatted_phone . '?text=' . urlencode($message);
    }
}
