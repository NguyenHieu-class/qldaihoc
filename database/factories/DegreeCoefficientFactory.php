<?php

namespace Database\Factories;

use App\Models\DegreeCoefficient;
use App\Models\Degree;
use Illuminate\Database\Eloquent\Factories\Factory;

class DegreeCoefficientFactory extends Factory
{
    protected $model = DegreeCoefficient::class;

    public function definition(): array
    {
        return [
            'degree_id' => Degree::factory(),
            'coefficient' => $this->faker->randomFloat(2, 1, 2),
        ];
    }
}
