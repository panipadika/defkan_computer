<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengguna_id');
            $table->enum('tipe', ['pesanan', 'servis'])->comment('Jenis referensi complaint');
            $table->unsignedBigInteger('id_referensi')->comment('id_pesanan atau id servis');

            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->json('foto_bukti')->nullable()->comment('Array path foto pendukung');

            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])
                ->default('menunggu');

            $table->text('respons_admin')->nullable();
            $table->timestamp('respons_at')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('pengguna_id')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('cascade');

            // Index untuk lookup cepat per tipe + id_referensi
            $table->index(['tipe', 'id_referensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint');
    }
};
