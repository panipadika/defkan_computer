<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengguna_id');
            $table->string('judul', 100)->nullable()->default('Percakapan');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->foreign('pengguna_id')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('user_id')->nullable()->comment('NULL jika dari admin');
            $table->text('pesan');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('chat_rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_rooms');
    }
};
