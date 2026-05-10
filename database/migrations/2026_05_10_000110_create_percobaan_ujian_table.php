<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('percobaan_ujian', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->unsignedTinyInteger('percobaan_ke');
            $table->dateTime('mulai_at')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->string('status', 30)->default('berlangsung');
            $table->decimal('nilai_pg', 5, 2)->nullable();
            $table->decimal('nilai_essay', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->boolean('is_lulus')->default(false);
            $table->timestamps();

            $table->unique(['ujian_id', 'mahasiswa_id', 'percobaan_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('percobaan_ujian');
    }
};