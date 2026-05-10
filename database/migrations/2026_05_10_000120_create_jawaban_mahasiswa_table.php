<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_mahasiswa', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('percobaan_ujian_id')->constrained('percobaan_ujian')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->foreignId('pilihan_jawaban_id')->nullable()->constrained('pilihan_jawaban')->nullOnDelete();
            $table->longText('jawaban_text')->nullable();
            $table->boolean('is_benar')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['percobaan_ujian_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_mahasiswa');
    }
};