<?php

namespace Tests\Unit;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;
use App\Models\ClassSizeCoefficient;
use App\Models\Degree;
use App\Models\Semester;
use App\Models\CourseOffering;
use App\Models\ClassSection;
use App\Services\TeachingPaymentService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeachingPaymentServiceTest extends TestCase
{
    use RefreshDatabase;
    public function test_calculate_payment()
    {
        Degree::create(['id' => 1, 'name' => 'Dr', 'coefficient' => 1.5]);
        $teacher = new Teacher(['degree_id' => 1]);
        $subject = new Subject(['coefficient' => 1.2]);

        TeachingRate::unguard();
        TeachingRate::create(['id' => 1, 'amount' => 100]);

        ClassSizeCoefficient::unguard();
        ClassSizeCoefficient::create(['min_students' => 1, 'max_students' => 50, 'coefficient' => 1.1]);

        $service = new TeachingPaymentService(100, ClassSizeCoefficient::all());
        $payment = $service->calculate($teacher, $subject, 30, 10);

        $this->assertEquals(100 * 1.5 * 1.1 * 1.2 * 10, $payment);
    }

    public function test_calculate_for_semester()
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

        $semester1 = Semester::factory()->create();
        $semester2 = Semester::factory()->create();

        $off1 = CourseOffering::factory()->create([
            'subject_id' => $subject->id,
            'semester_id' => $semester1->id,
        ]);
        $off2 = CourseOffering::factory()->create([
            'subject_id' => $subject->id,
            'semester_id' => $semester2->id,
        ]);

        ClassSection::factory()->create([
            'course_offering_id' => $off1->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'period_count' => 10,
            'student_count' => 30,
        ]);

        ClassSection::factory()->create([
            'course_offering_id' => $off2->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'period_count' => 10,
            'student_count' => 30,
        ]);

        $service = new TeachingPaymentService($rate->amount, ClassSizeCoefficient::all());

        $total = $service->calculateForSemester($teacher, $semester1->id);

        $expected = 100 * 1.2 * 1.1 * 1.5 * 10;
        $this->assertEquals($expected, $total);
    }
}
