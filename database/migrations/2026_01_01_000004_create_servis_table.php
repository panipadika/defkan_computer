<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimasi_servis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('jenis_kerusakan', 200)->unique();
            $table->decimal('harga_estimasi', 12, 2)->default(0);
            $table->string('estimasi_durasi', 50)->nullable()->default('1-3 Hari Kerja');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('servis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengguna_id');
            $table->string('kode_servis', 50)->unique();
            $table->string('merek_laptop', 100);
            $table->string('jenis_kerusakan', 200);
            $table->text('deskripsi')->nullable();
            $table->text('keterangan')->nullable()->comment('Catatan teknisi');
            $table->decimal('estimasi_biaya', 12, 2)->nullable();
            $table->string('estimasi_durasi', 50)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->enum('status', ['menunggu', 'diperiksa', 'dikerjakan', 'selesai', 'diambil'])->default('menunggu');
            $table->timestamps();

            $table->foreign('pengguna_id')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servis');
        Schema::dropIfExists('estimasi_servis');
    }
};
