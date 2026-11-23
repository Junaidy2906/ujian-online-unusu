<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_week_settings', function (Blueprint $table): void {
            $table->id();
            $table->date('week_1_start_date');
            $table->unsignedTinyInteger('meeting_day'); // 1 (Senin) - 7 (Minggu)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_week_settings');
    }
};
