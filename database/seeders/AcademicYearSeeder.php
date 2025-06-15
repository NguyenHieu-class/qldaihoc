<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $start = date('Y') - 1;
        for ($i = 0; $i < 3; $i++) {
            $year = $start + $i;
            AcademicYear::create(['name' => $year.'-'.($year + 1)]);
        }
    }
}
