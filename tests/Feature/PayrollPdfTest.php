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

class PayrollPdfTest extends TestCase
{
    use RefreshDatabase;

    private function setUpData(): Teacher
    {
        $rate = TeachingRate::factory()->create(['amount' => 100]);
        ClassSizeCoefficient::factory()->create([
            'min_students' => 0,
            'max_students' => 50,
            'coefficient' => 1.1,
        ]);
        $degree = Degree::factory()->create(['coefficient' => 1.2]);
        $subject = Subject::factory()->create(['coefficient' => 1.5]);
        $teacher = Teacher::factory()->create(['degree_id' => $degree->id]);
        ClassSection::factory()->create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'teaching_rate_id' => $rate->id,
            'period_count' => 10,
            'student_count' => 30,
        ]);
        return $teacher;
    }

    public function test_export_routes_return_pdf(): void
    {
        $teacher = $this->setUpData();
        $admin = User::factory()->create(['role' => 'admin']);

        $res = $this->actingAs($admin)->get(route('payrolls.export'));
        $res->assertOk();
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));

        $res = $this->actingAs($admin)->get(route('payrolls.export_detail', $teacher));
        $res->assertOk();
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));

        $section = ClassSection::first();
        $res = $this->actingAs($admin)->get(route('payrolls.section_export', $section));
        $res->assertOk();
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));
    }
}
