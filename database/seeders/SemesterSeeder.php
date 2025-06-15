<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $years = AcademicYear::all();
        foreach ($years as $year) {
            foreach ([1,2] as $i) {
                Semester::create([
                    'name' => 'HK'.$i,
                    'academic_year_id' => $year->id,
                ]);
            }
        }
    }
}
