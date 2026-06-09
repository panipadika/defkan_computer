<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengguna_id');
            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah')->default(1);
            $table->timestamps();

            $table->unique(['pengguna_id', 'id_produk']);
            $table->foreign('pengguna_id')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
