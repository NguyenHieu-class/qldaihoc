<?php

namespace App\Services;

use App\Models\TeachingRate;
use App\Models\DegreeCoefficient;
use App\Models\ClassSizeCoefficient;
use App\Models\Teacher;
use App\Models\Subject;

class TeachingPaymentService
{
    public function calculate(Teacher $teacher, Subject $subject, int $studentCount, int $periods): float
    {
        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;

        $degreeCoefficient = DegreeCoefficient::where('degree_id', $teacher->degree_id)
            ->value('coefficient') ?? 1;

        $classCoefficient = ClassSizeCoefficient::where('min_students', '<=', $studentCount)
            ->where('max_students', '>=', $studentCount)
            ->value('coefficient') ?? 1;

        $coefficient = $subject->coefficient ?? 1;

        return $base * $degreeCoefficient * $classCoefficient * $coefficient * $periods;
    }
}
