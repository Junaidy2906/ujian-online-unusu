<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->string('nama_ujian', 150);
            $table->text('deskripsi')->nullable();
            $table->dateTime('jadwal_mulai');
            $table->dateTime('jadwal_selesai')->nullable();
            $table->unsignedInteger('durasi_menit')->default(60);
            $table->decimal('nilai_minimum_lulus', 5, 2)->default(0);
            $table->unsignedTinyInteger('maksimal_percobaan')->default(3);
            $table->decimal('bobot_pg', 5, 2)->default(70);
            $table->decimal('bobot_essay', 5, 2)->default(30);
            $table->string('status', 30)->default('draft');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};