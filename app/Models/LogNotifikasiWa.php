<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogNotifikasiWa extends Model
{
    use HasFactory;

    protected $table = 'log_notifikasi_wa';

    protected $fillable = [
        'order_id',
        'nomor_wa',
        'pesan',
        'status',
        'waktu_kirim',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getPhoneNumberAttribute() { return $this->attributes['nomor_wa'] ?? ''; }
    public function getMessageAttribute() { return $this->attributes['pesan'] ?? ''; }
    public function getSentAtAttribute() { return $this->attributes['waktu_kirim'] ?? null; }

    public static function catatLog(int $orderId, string $phone, string $message, string $status, ?string $responsePayload = null, ?string $deviceToken = null): self
    {
        return self::create([
            'order_id' => $orderId,
            'nomor_wa' => $phone,
            'pesan' => $message,
            'status' => $status,
            'waktu_kirim' => ($status === 'sent') ? now() : null,
        ]);
    }
}
