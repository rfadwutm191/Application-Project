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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_laundry')->default('Bluewash Laundry');
            $table->text('alamat_laundry')->nullable();
            $table->string('no_telepon_laundry', 50)->nullable();
            $table->text('header_struk')->nullable();
            $table->text('footer_struk')->nullable();
            $table->boolean('wa_auto_send')->default(true);
            $table->string('wa_device_token')->nullable();
            $table->text('template_pesan_wa')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
