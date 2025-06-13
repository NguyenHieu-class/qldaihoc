<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('degree_coefficients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('degree_id')->constrained()->onDelete('cascade');
            $table->decimal('coefficient', 5, 2);
            $table->timestamps();
            $table->unique('degree_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('degree_coefficients');
    }
};
