<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_id' => 'SV' . $this->faker->unique()->numerify('###'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date(),
            'gender' => 'Nam',
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'class_id' => Classes::factory(),
            'user_id' => User::factory(),
        ];
    }
}
