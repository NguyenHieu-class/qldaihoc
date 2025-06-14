<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassesFactory extends Factory
{
    protected $model = Classes::class;

    public function definition(): array
    {
        return [
            'name' => 'Class ' . $this->faker->unique()->numerify('##'),
            'code' => strtoupper($this->faker->unique()->lexify('CL??')),
            'major_id' => Major::factory(),
            'year' => 2024,
        ];
    }
}
