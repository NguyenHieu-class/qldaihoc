<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('faculty_id')->nullable()->after('difficulty_ratio')->constrained();
        });

        $facultyId = DB::table('faculties')->value('id');
        if ($facultyId) {
            DB::table('subjects')->whereNull('faculty_id')->update(['faculty_id' => $facultyId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('faculty_id');
        });
    }
};
