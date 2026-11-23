<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_meetings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->unsignedTinyInteger('meeting_number');
            $table->timestamps();

            $table->unique(['mata_kuliah_id', 'semester_id', 'meeting_number'], 'lms_meetings_unique_course_semester_meeting');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_meetings');
    }
};
