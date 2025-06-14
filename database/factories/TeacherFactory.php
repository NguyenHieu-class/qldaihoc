<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\Faculty;
use App\Models\Degree;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'teacher_id' => 'T' . $this->faker->unique()->numerify('###'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date(),
            'gender' => 'Nam',
            'email' => $this->faker->unique()->safeEmail,
            'faculty_id' => Faculty::factory(),
            'degree_id' => Degree::factory(),
            'user_id' => null,
        ];
    }
}
