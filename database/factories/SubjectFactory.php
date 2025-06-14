<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'code' => strtoupper($this->faker->unique()->lexify('SUB??')),
            'credits' => 3,
            'description' => $this->faker->sentence,
            'difficulty_ratio' => 1,
            'faculty_id' => Faculty::factory(),
        ];
    }
}
