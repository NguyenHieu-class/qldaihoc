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
        Schema::create('tuition_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('per_credit_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        DB::table('tuition_settings')->insert([
            'per_credit_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_settings');
    }
};
