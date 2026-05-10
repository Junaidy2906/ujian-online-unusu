<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilihan_jawaban', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->string('kode', 5);
            $table->text('jawaban');
            $table->boolean('is_benar')->default(false);
            $table->timestamps();

            $table->unique(['soal_id', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilihan_jawaban');
    }
};