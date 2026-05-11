<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian', function (Blueprint $table): void {
            $table->string('kode_soal', 30)->nullable()->unique()->after('nama_ujian');
        });

        Schema::create('ujian_mahasiswa', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->string('kode_soal', 30)->nullable();
            $table->timestamps();

            $table->unique(['ujian_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_mahasiswa');

        Schema::table('ujian', function (Blueprint $table): void {
            $table->dropUnique(['kode_soal']);
            $table->dropColumn('kode_soal');
        });
    }
};
