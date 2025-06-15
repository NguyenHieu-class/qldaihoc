<?php

namespace Database\Factories;

use App\Models\ClassSizeCoefficient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassSizeCoefficientFactory extends Factory
{
    protected $model = ClassSizeCoefficient::class;

    public function definition(): array
    {
        $min = $this->faker->numberBetween(1, 50);
        return [
            'min_students' => $min,
            'max_students' => $min + $this->faker->numberBetween(1, 50),
            'coefficient' => $this->faker->randomFloat(2, 1, 2),
        ];
    }
}
