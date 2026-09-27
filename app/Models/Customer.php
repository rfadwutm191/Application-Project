<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'status',
    ];

    protected $appends = [
        'name',
        'phone',
        'address',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Accessor backward compatibility helpers if accessed as name/phone/address
    public function getNameAttribute() { return $this->attributes['nama'] ?? ''; }
    public function getPhoneAttribute() { return $this->attributes['telepon'] ?? ''; }
    public function getAddressAttribute() { return $this->attributes['alamat'] ?? ''; }
    public function getStatusAttribute() { return $this->attributes['status'] ?? 'aktif'; }
}
