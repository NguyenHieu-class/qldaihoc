<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Student;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $sections = ClassSection::all();

        foreach ($sections as $section) {
            $count = min(5, $students->count());
            $selected = $students->random($count);
            foreach ($selected as $student) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'class_section_id' => $section->id,
                ]);
                $section->increment('student_count');
            }
        }
    }
}
