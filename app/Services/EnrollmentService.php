<?php

namespace App\Services;

use App\Exceptions\EnrollmentException;
use App\Models\ClassSection;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Tuition;
use App\Models\TuitionSetting;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function enroll(Student $student, ClassSection $classSection): void
    {
        if ($classSection->students()->where('enrollments.student_id', $student->id)->exists()) {
            throw new EnrollmentException('Bạn đã đăng ký lớp này.');
        }

        $classSection->loadMissing('courseOffering.semester', 'subject');

        if ($classSection->status !== ClassSection::STATUS_OPEN) {
            throw new EnrollmentException('Lớp học phần hiện không mở để đăng ký.');
        }

        $courseOffering = $classSection->courseOffering;
        if ($courseOffering && $courseOffering->status !== CourseOffering::STATUS_OPEN) {
            throw new EnrollmentException('Môn học này hiện không mở để đăng ký.');
        }

        if ($classSection->students()->count() >= $classSection->student_count) {
            throw new EnrollmentException('Lớp đã đủ số lượng.');
        }

        DB::transaction(function () use ($student, $classSection) {
            Enrollment::create([
                'student_id' => $student->id,
                'class_section_id' => $classSection->id,
            ]);

            $this->createTuitionForEnrollment($student, $classSection);
        });
    }

    protected function createTuitionForEnrollment(Student $student, ClassSection $classSection): void
    {
        $setting = TuitionSetting::first();
        $perCreditAmount = $setting?->per_credit_amount ?? 0;

        $subject = $classSection->subject;
        if (! $subject) {
            return;
        }

        $coefficient = $subject->coefficient ?: 1;
        $credits = $subject->credits ?: 0;

        $amount = round($perCreditAmount * $coefficient * $credits, 2);

        Tuition::updateOrCreate(
            [
                'student_id' => $student->id,
                'class_section_id' => $classSection->id,
            ],
            [
                'semester_id' => optional($classSection->courseOffering)->semester_id,
                'amount' => $amount,
                'status' => Tuition::STATUS_PENDING,
            ]
        );
    }
}
