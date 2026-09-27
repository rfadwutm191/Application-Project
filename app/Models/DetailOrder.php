<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    use HasFactory;

    protected $table = 'detail_orders';

    protected $fillable = [
        'order_id',
        'service_id',
        'harga_satuan',
        'berat',
        'jumlah',
        'is_express',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Accessors for backward compatibility and dynamic lookup from services table
    public function getNamaLayananAttribute()
    {
        return $this->attributes['nama_layanan'] ?? ($this->service ? $this->service->nama_layanan : 'Layanan Laundry');
    }

    public function getServiceNameAttribute()
    {
        return $this->nama_layanan;
    }

    public function getTipeLayananAttribute()
    {
        return $this->attributes['tipe_layanan'] ?? ($this->service ? $this->service->tipe : 'kg');
    }

    public function getServiceTypeAttribute()
    {
        return $this->tipe_layanan;
    }

    public function getPricePerUnitAttribute() { return $this->attributes['harga_satuan'] ?? 0; }
    public function setPricePerUnitAttribute($val) { $this->attributes['harga_satuan'] = $val; }

    public function getWeightAttribute() { return $this->attributes['berat'] ?? 0; }
    public function setWeightAttribute($val) { $this->attributes['berat'] = $val; }

    public function getQtyAttribute() { return $this->attributes['jumlah'] ?? 1; }
    public function setQtyAttribute($val) { $this->attributes['jumlah'] = $val; }

    public function calculateSubtotal(): int
    {
        $unitPrice = $this->harga_satuan;
        $qtyWeight = ($this->tipe_layanan === 'kg') ? (float)$this->berat : (int)$this->jumlah;
        $base = (int)round($unitPrice * $qtyWeight);
        $expressFee = $this->is_express ? 10000 : 0;
        
        $this->subtotal = $base + $expressFee;
        return $this->subtotal;
    }
}
