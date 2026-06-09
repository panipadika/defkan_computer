<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->bigIncrements('id_pengguna');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('no_hp', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
