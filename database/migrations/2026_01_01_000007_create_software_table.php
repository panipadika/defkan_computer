<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 100);
            $table->string('icon', 255)->nullable()->comment('URL logo asli / Emoji');
            $table->string('kategori', 50)->nullable();
        });

        Schema::create('requirement_software', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('software_id');
            $table->integer('ram_min')->default(4)->comment('Minimum RAM dalam GB');
            $table->integer('storage_min')->default(256)->comment('Minimum Storage dalam GB');
            $table->string('vga_min')->nullable();
            $table->string('cpu_min')->nullable();

            $table->foreign('software_id')->references('id')->on('software')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirement_software');
        Schema::dropIfExists('software');
    }
};
