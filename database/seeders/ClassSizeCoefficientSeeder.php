<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassSizeCoefficient;

class ClassSizeCoefficientSeeder extends Seeder
{
    public function run(): void
    {
        $ranges = [
            ['min_students' => 1, 'max_students' => 30, 'coefficient' => 1.0],
            ['min_students' => 31, 'max_students' => 60, 'coefficient' => 1.1],
            ['min_students' => 61, 'max_students' => 100, 'coefficient' => 1.2],
        ];

        foreach ($ranges as $data) {
            ClassSizeCoefficient::create($data);
        }
    }
}
