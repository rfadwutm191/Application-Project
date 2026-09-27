<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'nama_laundry' => 'Bluewash Laundry POS',
                'alamat_laundry' => 'Jl. Merdeka No. 45, Bandung',
                'no_telepon_laundry' => '081234567890',
                'header_struk' => 'Terima kasih telah mempercayakan pakaian Anda di Bluewash Laundry!',
                'template_pesan_wa' => "Halo kak {nama}, cucian Anda dengan no. nota *{nota}* ({layanan}) di *{toko}* sudah SELESAI & SIAP DIAMBIL! Terima kasih!",
                'updated_by' => $admin->id,
            ]
        );

        // 3. Seed Master Services (Katalog Layanan Laundry)
        \App\Models\Service::create(['nama_layanan' => 'Cuci Setrika (Komplit)', 'tipe' => 'kg', 'harga' => 7000, 'durasi' => 2]);
        \App\Models\Service::create(['nama_layanan' => 'Setrika Saja', 'tipe' => 'kg', 'harga' => 4000, 'durasi' => 1]);
        \App\Models\Service::create(['nama_layanan' => 'Cuci Kering Saja', 'tipe' => 'kg', 'harga' => 5000, 'durasi' => 1]);
        \App\Models\Service::create(['nama_layanan' => 'Dry Cleaning Jas / Blazer', 'tipe' => 'pcs', 'harga' => 25000, 'durasi' => 3]);
        \App\Models\Service::create(['nama_layanan' => 'Dry Cleaning Bed Cover', 'tipe' => 'pcs', 'harga' => 35000, 'durasi' => 3]);
        \App\Models\Service::create(['nama_layanan' => 'Cuci Sepatu Premium', 'tipe' => 'pcs', 'harga' => 30000, 'durasi' => 2]);
    }
}

