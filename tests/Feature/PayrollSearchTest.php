<?php

namespace Tests\Feature;

use App\Models\ClassSection;
use App\Models\ClassSizeCoefficient;
use App\Models\Degree;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollSearchTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): array
    {
        $rate = TeachingRate::factory()->create(['amount' => 100]);
        ClassSizeCoefficient::factory()->create(['min_students' => 0, 'max_students' => 50, 'coefficient' => 1]);
        $degree = Degree::factory()->create(['coefficient' => 1]);
        $subject = Subject::factory()->create(['coefficient' => 1]);

        $teacher1 = Teacher::factory()->create([
            'teacher_id' => 'T100',
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'degree_id' => $degree->id,
        ]);
        $teacher2 = Teacher::factory()->create([
            'teacher_id' => 'T200',
            'first_name' => 'Bob',
            'last_name' => 'Brown',
            'degree_id' => $degree->id,
        ]);

        ClassSection::factory()->create([
            'teacher_id' => $teacher1->id,
            'subject_id' => $subject->id,
            'teaching_rate_id' => $rate->id,
            'period_count' => 10,
            'student_count' => 30,
        ]);
        ClassSection::factory()->create([
            'teacher_id' => $teacher2->id,
            'subject_id' => $subject->id,
            'teaching_rate_id' => $rate->id,
            'period_count' => 10,
            'student_count' => 30,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        return [$admin, $teacher1, $teacher2];
    }

    public function test_search_filters_sections(): void
    {
        [$admin, $teacher1, $teacher2] = $this->seedData();

        $response = $this->actingAs($admin)->get(route('payrolls.index', [
            'search' => $teacher1->teacher_id,
        ]));

        $response->assertOk();
        $sections = $response->viewData('sections');
        $this->assertCount(1, $sections);
        $this->assertEquals($teacher1->id, $sections->first()->teacher_id);
    }

    public function test_search_applied_to_pdf_export(): void
    {
        [$admin, $teacher1] = $this->seedData();

        $pdf = $this->actingAs($admin)->get(route('payrolls.export', [
            'search' => $teacher1->teacher_id,
        ]));

        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
    }
}
