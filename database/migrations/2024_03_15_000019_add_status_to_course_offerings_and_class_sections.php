<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->string('status')->default('open')->after('semester_id');
        });

        Schema::table('class_sections', function (Blueprint $table) {
            $table->string('status')->default('open')->after('student_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_sections', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
