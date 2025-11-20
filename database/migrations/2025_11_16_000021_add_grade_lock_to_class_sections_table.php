<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_sections', function (Blueprint $table) {
            $table->boolean('grades_locked')->default(false)->after('payment_status');
            $table->timestamp('grades_locked_at')->nullable()->after('grades_locked');
        });
    }

    public function down(): void
    {
        Schema::table('class_sections', function (Blueprint $table) {
            $table->dropColumn(['grades_locked', 'grades_locked_at']);
        });
    }
};
