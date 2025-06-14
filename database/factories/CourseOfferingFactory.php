<?php

namespace Database\Factories;

use App\Models\CourseOffering;
use App\Models\Subject;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseOfferingFactory extends Factory
{
    protected $model = CourseOffering::class;

    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'semester_id' => Semester::factory(),
        ];
    }
}
