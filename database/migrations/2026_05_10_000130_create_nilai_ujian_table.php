<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ujian', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('percobaan_ujian_id')->constrained('percobaan_ujian')->cascadeOnDelete();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->decimal('nilai_pg', 5, 2)->nullable();
            $table->decimal('nilai_essay', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('status_penilaian', 30)->default('menunggu_koreksi');
            $table->boolean('status_lulus')->default(false);
            $table->text('catatan')->nullable();
            $table->dateTime('dinilai_at')->nullable();
            $table->timestamps();

            $table->unique('percobaan_ujian_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ujian');
    }
};