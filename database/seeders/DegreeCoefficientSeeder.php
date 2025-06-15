<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Degree;
use App\Models\DegreeCoefficient;

class DegreeCoefficientSeeder extends Seeder
{
    public function run(): void
    {
        $degrees = [
            ['name' => 'Cu nhan', 'coeff' => 1.0],
            ['name' => 'Thac si', 'coeff' => 1.2],
            ['name' => 'Tien si', 'coeff' => 1.5],
        ];

        foreach ($degrees as $data) {
            $degree = Degree::create([
                'name' => $data['name'],
                'coefficient' => $data['coeff'],
            ]);
            DegreeCoefficient::create([
                'degree_id' => $degree->id,
                'coefficient' => $data['coeff'],
            ]);
        }
    }
}
