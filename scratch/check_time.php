<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "App Timezone: " . config('app.timezone') . "\n";
echo "Current Time: " . now()->format('Y-m-d H:i:s T') . "\n\n";

$orders = \App\Models\Order::latest()->take(10)->get();
echo "Recent Orders:\n";
foreach ($orders as $order) {
    echo "ID: {$order->id} | Nota: {$order->kode_nota} | Created: " . optional($order->created_at)->format('d M Y H:i') . " | DateIn: " . optional($order->date_in)->format('d M Y H:i') . "\n";
}
