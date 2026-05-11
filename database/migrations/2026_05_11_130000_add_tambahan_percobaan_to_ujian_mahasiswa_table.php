<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian_mahasiswa', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tambahan_percobaan')->default(0)->after('kode_soal');
        });
    }

    public function down(): void
    {
        Schema::table('ujian_mahasiswa', function (Blueprint $table): void {
            $table->dropColumn('tambahan_percobaan');
        });
    }
};

