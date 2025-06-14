<?php

namespace Tests\Unit;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;
use App\Models\DegreeCoefficient;
use App\Models\ClassSizeCoefficient;
use App\Models\Degree;
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
        $subject = new Subject(['difficulty_ratio' => 1.2]);

        TeachingRate::unguard();
        TeachingRate::create(['id' => 1, 'amount' => 100]);

        DegreeCoefficient::unguard();
        DegreeCoefficient::create(['degree_id' => 1, 'coefficient' => 1.5]);

        ClassSizeCoefficient::unguard();
        ClassSizeCoefficient::create(['min_students' => 1, 'max_students' => 50, 'coefficient' => 1.1]);

        $service = new TeachingPaymentService();
        $payment = $service->calculate($teacher, $subject, 30, 10);

        $this->assertEquals(100 * 1.5 * 1.1 * 1.2 * 10, $payment);
    }
}
