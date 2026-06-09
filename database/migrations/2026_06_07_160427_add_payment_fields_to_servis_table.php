<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            if (!Schema::hasColumn('servis', 'no_wa')) {
                $table->string('no_wa', 25)->nullable()->after('pengguna_id');
            }

            if (!Schema::hasColumn('servis', 'nama_perangkat')) {
                $table->string('nama_perangkat', 150)->nullable()->after('merek_laptop');
            }

            if (!Schema::hasColumn('servis', 'total_biaya')) {
                $table->decimal('total_biaya', 12, 2)->nullable()->after('estimasi_biaya');
            }

            if (!Schema::hasColumn('servis', 'metode_pembayaran')) {
                $table->string('metode_pembayaran', 100)->nullable()->after('total_biaya');
            }

            if (!Schema::hasColumn('servis', 'status_pembayaran')) {
                $table->string('status_pembayaran', 50)->default('pending')->after('metode_pembayaran');
            }

            if (!Schema::hasColumn('servis', 'waktu_pembayaran')) {
                $table->timestamp('waktu_pembayaran')->nullable()->after('status_pembayaran');
            }

            if (!Schema::hasColumn('servis', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('waktu_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $columns = [
                'no_wa',
                'nama_perangkat',
                'total_biaya',
                'metode_pembayaran',
                'status_pembayaran',
                'waktu_pembayaran',
                'bukti_pembayaran',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('servis', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
