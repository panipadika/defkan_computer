<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengguna_id');
            $table->enum('tipe', ['produk', 'servis']);

            // Untuk ulasan produk (dari pesanan)
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->unsignedBigInteger('id_produk')->nullable();

            // Untuk ulasan servis
            $table->unsignedBigInteger('id_servis')->nullable();

            $table->tinyInteger('rating')->unsigned()->comment('1-5 bintang');
            $table->text('komentar')->nullable();
            $table->json('foto_bukti')->nullable()->comment('Array path foto opsional');

            // Admin bisa hapus review tidak pantas (soft delete cukup dengan flag)
            $table->boolean('is_visible')->default(true)->comment('False = disembunyikan admin');

            $table->timestamps();

            // Foreign keys
            $table->foreign('pengguna_id')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->onDelete('cascade');
            $table->foreign('id_servis')->references('id')->on('servis')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');

            // Constraint: 1 review per produk per pesanan per pengguna
            $table->unique(['pengguna_id', 'id_pesanan', 'id_produk'], 'unique_ulasan_produk');

            // Constraint: 1 review per servis per pengguna
            $table->unique(['pengguna_id', 'id_servis'], 'unique_ulasan_servis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
