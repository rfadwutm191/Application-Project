<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = \App\Models\Setting::instance();
$s->footer_struk = "1. Pengambilan cucian wajib membawa nota resi ini.\n2. Kerusakan/luntur di luar tanggung jawab jika tidak dilaporkan sebelumnya.\n3. Garansi cuci ulang 1x24 jam.";
$s->save();

echo "Updated footer_struk:\n" . $s->footer_struk . "\n";
