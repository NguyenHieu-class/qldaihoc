<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_grade_can_be_created_with_note(): void
    {
        $faculty = Faculty::create(['name' => 'F1', 'code' => 'F1']);
        $major = Major::create(['name' => 'M1', 'code' => 'M1', 'faculty_id' => $faculty->id]);
        $class = Classes::create(['name' => 'C1', 'code' => 'C1', 'major_id' => $major->id, 'year' => 2024]);
        $student = Student::create([
            'student_id'    => 'SV0001',
            'first_name'    => 'A',
            'last_name'     => 'B',
            'date_of_birth' => '2000-01-01',
            'gender'        => 'Nam',
            'email'         => 'a@example.com',
            'phone'         => '123456789',
            'address'       => 'abc',
            'class_id'      => $class->id,
            'user_id'       => null,
        ]);
        $subject = Subject::create([
            'name'    => 'Subj',
            'code'    => 'SUB1',
            'credits' => 3,
        ]);

        $grade = Grade::create([
            'student_id'       => $student->id,
            'subject_id'       => $subject->id,
            'midterm_score'    => 5,
            'final_score'      => 6,
            'assignment_score' => 7,
            'semester'         => 'Học kỳ 1',
            'academic_year'    => 2024,
            'note'             => 'Excellent',
        ]);

        $this->assertDatabaseHas('grades', [
            'id'   => $grade->id,
            'note' => 'Excellent',
        ]);
    }
}
