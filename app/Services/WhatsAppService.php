<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\LogNotifikasiWa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }
        return $cleaned;
    }

    public static function buildMessage(Order $order): string
    {
        $storeName = Setting::get('store_name', 'Bluewash Laundry');
        $defaultTemplate = "Halo kak {nama}, cucian Anda dengan no. nota *{nota}* ({layanan}) di *{toko}* sudah SELESAI & SIAP DIAMBIL! Terima kasih!";
        $template = Setting::get('wa_template', $defaultTemplate);
        
        $details = $order->detailOrders;
        if ($details && $details->count() > 1) {
            $serviceDetails = $details->map(function($d) {
                $unit = $d->service_type === 'kg' ? number_format((float)($d->weight ?? 0), 1).' Kg' : ($d->qty ?? 1).' Pcs';
                return $d->service_name . ' (' . $unit . ')';
            })->implode(', ');
        } else {
            $serviceDetails = $order->service_name;
        }

        return str_replace(
            ['{nama}', '{nota}', '{layanan}', '{toko}', '{total}'],
            [
                $order->customer_name,
                $order->order_code,
                $serviceDetails ?: 'Layanan Laundry',
                $storeName,
                number_format($order->total_price, 0, ',', '.')
            ],
            $template
        );
    }

    // Kirim notifikasi WA siap diambil via Fonnte API
    public static function sendOrderReadyNotification(Order $order): array
    {
        $autoSend = Setting::get('wa_auto_send', '1');
        if ($autoSend !== '1' && $autoSend !== 1 && $autoSend !== true && $autoSend !== 'on') {
            return [
                'success' => false,
                'message' => 'Notifikasi WhatsApp otomatis dinonaktifkan di pengaturan.'
            ];
        }

        $token = Setting::get('wa_gateway_token', env('FONNTE_TOKEN', ''));
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Fonnte API Token belum diatur di menu Pengaturan Toko.'
            ];
        }

        $phone = self::formatPhoneNumber($order->customer_phone);
        $message = self::buildMessage($order);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(10)->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $payloadJson = $response->body();

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['status']) && $result['status'] === true) {
                    // Record success log
                    LogNotifikasiWa::catatLog(
                        $order->id,
                        $phone,
                        $message,
                        'sent',
                        $payloadJson,
                        $token
                    );

                    return [
                        'success' => true,
                        'message' => 'Notifikasi WhatsApp berhasil dikirim ke pelanggan!'
                    ];
                }

                // Record failed log
                LogNotifikasiWa::catatLog(
                    $order->id,
                    $phone,
                    $message,
                    'failed',
                    $payloadJson,
                    $token
                );

                return [
                    'success' => false,
                    'message' => 'Gagal mengirim WA (Fonnte): ' . ($result['reason'] ?? 'Response error.')
                ];
            }

            // Record failed log
            LogNotifikasiWa::catatLog(
                $order->id,
                $phone,
                $message,
                'failed',
                $payloadJson,
                $token
            );

            return [
                'success' => false,
                'message' => 'HTTP Request gagal ke Fonnte API (Status: ' . $response->status() . ').'
            ];
        } catch (\Exception $e) {
            Log::error("Fonnte WA Exception: " . $e->getMessage());

            LogNotifikasiWa::catatLog(
                $order->id,
                $phone,
                $message,
                'failed',
                json_encode(['error' => $e->getMessage()]),
                $token
            );

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat mengirim WA: ' . $e->getMessage()
            ];
        }
    }
}
