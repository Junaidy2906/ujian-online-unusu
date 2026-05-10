<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table): void {
            if (! Schema::hasColumn('dosen', 'nidn')) {
                $table->string('nidn', 30)->nullable()->unique()->after('user_id');
            }
        });

        if (Schema::hasColumn('dosen', 'nip') && Schema::hasColumn('dosen', 'nidn')) {
            DB::statement("UPDATE dosen SET nidn = nip WHERE nidn IS NULL AND nip IS NOT NULL");
        }

        Schema::table('mahasiswa', function (Blueprint $table): void {
            if (! Schema::hasColumn('mahasiswa', 'prodi')) {
                $table->string('prodi', 100)->nullable()->after('nim');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table): void {
            if (Schema::hasColumn('dosen', 'nidn')) {
                $table->dropUnique('dosen_nidn_unique');
                $table->dropColumn('nidn');
            }
        });

        Schema::table('mahasiswa', function (Blueprint $table): void {
            if (Schema::hasColumn('mahasiswa', 'prodi')) {
                $table->dropColumn('prodi');
            }
        });
    }
};
