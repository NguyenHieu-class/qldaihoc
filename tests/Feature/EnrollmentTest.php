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
        $faculty = Faculty::factory()->create();
        $major = Major::factory()->create(['faculty_id' => $faculty->id]);
        $class = Classes::factory()->create(['major_id' => $major->id]);

        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create([
            'class_id' => $class->id,
            'user_id' => $studentUser->id,
        ]);

        $otherUser = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::factory()->create([
            'class_id' => $class->id,
            'user_id' => $otherUser->id,
        ]);

        $degree = Degree::factory()->create();
        $teacher = Teacher::factory()->create([
            'faculty_id' => $faculty->id,
            'degree_id' => $degree->id,
        ]);

        $subject = Subject::factory()->create(['faculty_id' => $faculty->id]);
        $year = AcademicYear::factory()->create();
        $semester = Semester::factory()->create(['academic_year_id' => $year->id]);
        $offering = CourseOffering::factory()->create([
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
        ]);
        $section = ClassSection::factory()->create([
            'course_offering_id' => $offering->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
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

    public function test_student_can_register_and_unregister(): void
    {
        [$user, $student, , , $section] = $this->setUpData();

        $this->actingAs($user)
            ->post(route('enrollments.store', $section->id))
            ->assertRedirect();

        $enrollment = Enrollment::first();
        $this->assertNotNull($enrollment);

        $this->actingAs($user)
            ->delete(route('enrollments.destroy', $enrollment->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }
}
