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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->float('midterm_score')->nullable();
            $table->float('final_score')->nullable();
            $table->float('assignment_score')->nullable();
            $table->float('total_score')->nullable();
            $table->string('semester');
            $table->integer('academic_year');
            $table->timestamps();
            
            // Đảm bảo mỗi sinh viên chỉ có một điểm cho mỗi môn học trong một học kỳ của năm học
            $table->unique(['student_id', 'subject_id', 'semester', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
}; 