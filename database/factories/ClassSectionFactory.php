<?php

namespace Database\Factories;

use App\Models\ClassSection;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassSectionFactory extends Factory
{
    protected $model = ClassSection::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('SEC??')),
            'course_offering_id' => CourseOffering::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'room' => 'R' . $this->faker->numberBetween(1, 10),
            'period_count' => 0,
            'student_count' => 30,
        ];
    }
}
