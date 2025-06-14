<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseOfferingTest extends TestCase
{
    use RefreshDatabase;

    public function test_offerings_filtered_by_faculty_and_semester(): void
    {
        $facultyA = Faculty::factory()->create();
        $facultyB = Faculty::factory()->create();

        $year = AcademicYear::factory()->create();
        $semester1 = Semester::factory()->create(['academic_year_id' => $year->id]);
        $semester2 = Semester::factory()->create(['academic_year_id' => $year->id]);

        $subjectA = Subject::factory()->create(['faculty_id' => $facultyA->id]);
        $subjectB = Subject::factory()->create(['faculty_id' => $facultyB->id]);

        $offering1 = CourseOffering::factory()->create([
            'subject_id' => $subjectA->id,
            'semester_id' => $semester1->id,
        ]);
        CourseOffering::factory()->create([
            'subject_id' => $subjectB->id,
            'semester_id' => $semester1->id,
        ]);
        CourseOffering::factory()->create([
            'subject_id' => $subjectA->id,
            'semester_id' => $semester2->id,
        ]);

        $filtered = CourseOffering::where('semester_id', $semester1->id)
            ->whereHas('subject', fn($q) => $q->where('faculty_id', $facultyA->id))
            ->get();

        $this->assertCount(1, $filtered);
        $this->assertTrue($filtered->first()->is($offering1));
    }
}
