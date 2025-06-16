<?php

namespace Tests\Feature;

use App\Models\ClassSizeCoefficient;
use App\Models\Degree;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;
use App\Services\TeachingPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_salary_calculation(): void
    {
        $rate = TeachingRate::factory()->create(['amount' => 100]);
        $degree = Degree::factory()->create(['coefficient' => 1.2]);
        ClassSizeCoefficient::factory()->create([
            'min_students' => 0,
            'max_students' => 50,
            'coefficient' => 1.1,
        ]);
        $subject = Subject::factory()->create(['coefficient' => 1.5]);
        $teacher = Teacher::factory()->create(['degree_id' => $degree->id]);

        $service = new TeachingPaymentService();
        $salary = $service->calculate($teacher, $subject, 30, 10);

        $expected = 100 * 1.2 * 1.1 * 1.5 * 10;
        $this->assertEquals($expected, $salary);
    }
}
