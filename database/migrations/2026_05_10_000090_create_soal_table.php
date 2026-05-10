<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->unsignedInteger('nomor')->default(1);
            $table->enum('tipe', ['pg', 'essay']);
            $table->longText('pertanyaan');
            $table->decimal('poin', 5, 2)->default(1);
            $table->string('jawaban_benar', 255)->nullable();
            $table->text('rubrik_penilaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};