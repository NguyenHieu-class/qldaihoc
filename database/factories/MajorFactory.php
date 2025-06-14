<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class MajorFactory extends Factory
{
    protected $model = Major::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle,
            'code' => strtoupper($this->faker->unique()->lexify('MJR??')),
            'description' => $this->faker->sentence,
            'faculty_id' => Faculty::factory(),
        ];
    }
}
