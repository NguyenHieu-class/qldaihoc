<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company,
            'code' => strtoupper($this->faker->unique()->lexify('FAC??')),
            'description' => $this->faker->sentence,
        ];
    }
}
