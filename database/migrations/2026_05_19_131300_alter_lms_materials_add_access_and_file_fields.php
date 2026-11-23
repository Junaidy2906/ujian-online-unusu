<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_materials', function (Blueprint $table): void {
            $table->foreignId('lms_meeting_id')->nullable()->after('dosen_id')->constrained('lms_meetings')->nullOnDelete();
            $table->string('access_code', 30)->nullable()->after('type');
            $table->string('file_path')->nullable()->after('description');
            $table->string('file_name')->nullable()->after('file_path');
            $table->string('file_mime', 100)->nullable()->after('file_name');
        });
    }

    public function down(): void
    {
        Schema::table('lms_materials', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('lms_meeting_id');
            $table->dropColumn(['access_code', 'file_path', 'file_name', 'file_mime']);
        });
    }
};
