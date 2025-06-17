<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\ClassSizeCoefficient;
use App\Models\CourseOffering;
use App\Models\Degree;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTotalsTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): array
    {
        $year = AcademicYear::factory()->create();
        $semester = Semester::factory()->create(['academic_year_id' => $year->id]);
        $otherSemester = Semester::factory()->create(['academic_year_id' => $year->id]);

        TeachingRate::factory()->create(['amount' => 100]);
        ClassSizeCoefficient::factory()->create(['min_students' => 0, 'max_students' => 50, 'coefficient' => 1]);
        $degree = Degree::factory()->create(['coefficient' => 1]);
        $subject = Subject::factory()->create(['coefficient' => 1]);
        $teacher = Teacher::factory()->create(['degree_id' => $degree->id]);

        $offering1 = CourseOffering::factory()->create(['subject_id' => $subject->id, 'semester_id' => $semester->id]);
        $offering2 = CourseOffering::factory()->create(['subject_id' => $subject->id, 'semester_id' => $otherSemester->id]);

        ClassSection::factory()->create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_offering_id' => $offering1->id,
            'period_count' => 10,
            'student_count' => 20,
        ]);

        ClassSection::factory()->create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'course_offering_id' => $offering2->id,
            'period_count' => 5,
            'student_count' => 20,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        return [$admin, $teacher, $year, $semester];
    }

    public function test_totals_filtered_by_semester(): void
    {
        [$admin, $teacher, $year, $semester] = $this->seedData();

        $response = $this->actingAs($admin)->get(route('payrolls.index', [
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('total', 1000.0);

        $pdf = $this->actingAs($admin)->get(route('payrolls.export', [
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
        ]));

        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
    }
}
