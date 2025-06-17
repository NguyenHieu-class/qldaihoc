<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseOffering;
use App\Models\ClassSection;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;

class CourseOfferingSeeder extends Seeder
{
    public function run(): void
    {
        $semesters = Semester::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $rates = TeachingRate::all();

        foreach ($semesters as $semester) {
            $offeredSubjects = $subjects->random(min(5, $subjects->count()));
            foreach ($offeredSubjects as $subject) {
                $offering = CourseOffering::create([
                    'subject_id' => $subject->id,
                    'semester_id' => $semester->id,
                ]);

                for ($i = 1; $i <= 2; $i++) {
                    ClassSection::create([
                        'code' => strtoupper($subject->code . $semester->id . $i),
                        'course_offering_id' => $offering->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teachers->random()->id,
                        'teaching_rate_id' => $rates->random()->id,
                        'room' => 'R' . rand(1, 10),
                        'period_count' => 0,
                        'student_count' => 0,
                    ]);
                }
            }
        }
    }
}
