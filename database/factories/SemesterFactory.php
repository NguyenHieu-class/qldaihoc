<?php

namespace Database\Factories;

use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'name' => 'HK' . $this->faker->unique()->numberBetween(1, 2),
            'academic_year_id' => AcademicYear::factory(),
        ];
    }
}
