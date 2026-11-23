<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->unsignedTinyInteger('week_number');
            $table->string('title', 180);
            $table->string('type', 40);
            $table->text('description')->nullable();
            $table->string('resource_url')->nullable();
            $table->timestamps();

            $table->index(['mata_kuliah_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_materials');
    }
};
