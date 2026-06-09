<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('id_produk');
            $table->string('nama_produk', 200);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2);
            $table->integer('stok')->default(0);
            $table->string('foto')->nullable()->comment('Path relatif file foto dari storage/app/public');
            $table->string('kategori', 50)->nullable();
            $table->integer('ram')->nullable()->comment('RAM dalam GB');
            $table->integer('storage')->nullable()->comment('Storage dalam GB');
            $table->string('vga', 100)->nullable();
            $table->string('cpu', 100)->nullable();
            $table->string('merek', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
