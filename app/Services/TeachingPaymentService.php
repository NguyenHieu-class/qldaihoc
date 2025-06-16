<?php

namespace App\Services;

use App\Models\TeachingRate;
use App\Models\ClassSizeCoefficient;
use App\Models\Teacher;
use App\Models\Subject;

class TeachingPaymentService
{
    public function calculate(Teacher $teacher, Subject $subject, int $studentCount, int $periods): float
    {
        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;

        $degreeCoefficient = $teacher->degree->coefficient ?? 1;

        $classCoefficient = ClassSizeCoefficient::where('min_students', '<=', $studentCount)
            ->where('max_students', '>=', $studentCount)
            ->value('coefficient') ?? 1;

        $coefficient = $subject->coefficient ?? 1;

        return $base * $degreeCoefficient * $classCoefficient * $coefficient * $periods;
    }
}
