<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_ekspedisi', function (Blueprint $table) {
            $table->bigIncrements('id_layanan_ekspedisi');
            $table->string('nama_layanan', 100);
            $table->string('kode_layanan', 20)->nullable();
            $table->decimal('biaya_ongkir', 12, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_ekspedisi');
    }
};
