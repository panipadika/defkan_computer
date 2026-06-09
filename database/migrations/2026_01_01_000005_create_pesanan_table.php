<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_pesanan');
            $table->unsignedBigInteger('id_pengguna');
            $table->unsignedBigInteger('id_layanan_ekspedisi')->nullable();
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->enum('status', ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('pending');
            $table->text('alamat_pengiriman')->nullable();
            $table->string('metode_pembayaran', 100)->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamp('waktu_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->foreign('id_layanan_ekspedisi')->references('id_layanan_ekspedisi')->on('layanan_ekspedisi')->onDelete('set null');
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_detail');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga', 15, 2);
            $table->timestamps();

            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
    }
};
