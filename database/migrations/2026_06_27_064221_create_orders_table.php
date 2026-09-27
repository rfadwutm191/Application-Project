<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_nota')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('diproses'); // diproses, siap_diambil, selesai, batal
            $table->integer('discount')->default(0); // nilai/persentase diskon, misal: 5 (5%) atau 10 (10%)
            $table->integer('discount_amount')->default(0); // hasil diskon rupiah, misal: Rp 5.000
            $table->integer('total_biaya')->default(0);
            $table->dateTime('estimasi_selesai')->nullable(); // Perkiraan cucian selesai
            $table->dateTime('tanggal_siap')->nullable(); // Waktu cucian siap diambil
            $table->dateTime('tanggal_diambil')->nullable(); // Waktu cucian diambil pelanggan
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
