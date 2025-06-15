<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingRate;

class TeachingRateSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([100000,120000,150000] as $amount) {
            TeachingRate::create(['amount' => $amount]);
        }
    }
}
