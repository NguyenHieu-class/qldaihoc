<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\CourseOffering;
use App\Models\Degree;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function setUpData()
    {
        $faculty = Faculty::create(['name' => 'F', 'code' => 'F']);
        $major = Major::create(['name' => 'M', 'code' => 'M', 'faculty_id' => $faculty->id]);
        $class = Classes::create(['name' => 'C', 'code' => 'C', 'major_id' => $major->id, 'year' => 2024]);
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'student_id' => 'SV1',
            'first_name' => 'A',
            'last_name' => 'B',
            'date_of_birth' => '2000-01-01',
            'gender' => 'Nam',
            'email' => 'a@example.com',
            'class_id' => $class->id,
            'user_id' => $studentUser->id,
        ]);
        $otherUser = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::create([
            'student_id' => 'SV2',
            'first_name' => 'C',
            'last_name' => 'D',
            'date_of_birth' => '2000-01-02',
            'gender' => 'Nam',
            'email' => 'b@example.com',
            'class_id' => $class->id,
            'user_id' => $otherUser->id,
        ]);
        $degree = Degree::create(['name' => 'Dr', 'coefficient' => 1]);
        $teacher = Teacher::create([
            'teacher_id' => 'T1',
            'first_name' => 'T',
            'last_name' => '1',
            'date_of_birth' => '1980-01-01',
            'gender' => 'Nam',
            'email' => 't@example.com',
            'faculty_id' => $faculty->id,
            'degree_id' => $degree->id,
        ]);
        $subject = Subject::create(['name' => 'Sub', 'code' => 'S', 'credits' => 3, 'faculty_id' => $faculty->id]);
        $year = AcademicYear::create(['name' => '2024-2025']);
        $semester = Semester::create(['name' => 'HK1', 'academic_year_id' => $year->id]);
        $offering = CourseOffering::create(['subject_id' => $subject->id, 'semester_id' => $semester->id]);
        $section = ClassSection::create([
            'code' => 'L1',
            'course_offering_id' => $offering->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'room' => 'R1',
            'period_count' => 0,
            'student_count' => 1,
        ]);

        return [$studentUser, $student, $otherUser, $otherStudent, $section];
    }

    public function test_student_cannot_register_when_class_full(): void
    {
        [$user1, $student1, $user2, $student2, $section] = $this->setUpData();

        $this->actingAs($user1)
            ->post(route('enrollments.store', $section->id))
            ->assertRedirect();

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student1->id,
            'class_section_id' => $section->id,
        ]);

        $this->actingAs($user2)
            ->post(route('enrollments.store', $section->id))
            ->assertSessionHas('error');

        $this->assertEquals(1, Enrollment::count());
    }
}
