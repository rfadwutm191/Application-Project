<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use Carbon\Carbon;

$orders = Order::all();
foreach ($orders as $order) {
    if ($order->created_at && $order->created_at->format('Y-m-d') === '2026-09-16') {
        // Add 7 hours if created during UTC time
        $order->created_at = Carbon::parse($order->created_at)->addHours(7);
        $order->updated_at = Carbon::parse($order->updated_at)->addHours(7);
        if ($order->date_in) {
            $order->date_in = Carbon::parse($order->date_in)->addHours(7);
        }
        if ($order->date_out) {
            $order->date_out = Carbon::parse($order->date_out)->addHours(7);
        }
        $order->save();
        echo "Updated order {$order->kode_nota} to date_in: {$order->date_in}\n";
    }
}
