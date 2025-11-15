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
        Schema::table('tuitions', function (Blueprint $table) {
            $table->foreignId('class_section_id')
                ->nullable()
                ->after('student_id')
                ->constrained()
                ->nullOnDelete();

            $table->unique(['student_id', 'class_section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tuitions', function (Blueprint $table) {
            $table->dropUnique('tuitions_student_id_class_section_id_unique');
            $table->dropConstrainedForeignId('class_section_id');
        });
    }
};
