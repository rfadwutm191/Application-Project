<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'tipe',
        'harga',
        'durasi',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getNameAttribute() { return $this->attributes['nama_layanan'] ?? ''; }
    public function getTypeAttribute() { return $this->attributes['tipe'] ?? 'kg'; }
    public function getPriceAttribute() { return $this->attributes['harga'] ?? 0; }
    public function getDurationAttribute() { return $this->attributes['durasi'] ?? 2; }
}
