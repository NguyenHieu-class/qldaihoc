<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Collection;

class TeachingPaymentService
{
    protected float $baseRate;

    /**
     * Collection of class size coefficient models.
     */
    protected Collection $classCoefficients;

    public function __construct(float $baseRate, Collection $classCoefficients)
    {
        $this->baseRate = $baseRate;
        $this->classCoefficients = $classCoefficients;
    }

    public function calculate(Teacher $teacher, Subject $subject, int $studentCount, int $periods): float
    {
        $degreeCoefficient = $teacher->degree->coefficient ?? 1;

        $classCoefficient = optional(
            $this->classCoefficients->first(function ($coef) use ($studentCount) {
                return $coef->min_students <= $studentCount && $coef->max_students >= $studentCount;
            })
        )->coefficient ?? 1;

        $subjectCoefficient = $subject->coefficient ?? 1;

        return $this->baseRate * $degreeCoefficient * $classCoefficient * $subjectCoefficient * $periods;
    }
}
