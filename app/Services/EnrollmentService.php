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
        $student->loadMissing('class.major');

        $studentFacultyId = $student->class?->major?->faculty_id;
        $sectionFacultyId = $classSection->subject?->faculty_id;

        if ($studentFacultyId && $sectionFacultyId && $studentFacultyId !== $sectionFacultyId) {
            throw new EnrollmentException('Sinh viên không thể đăng ký lớp học phần thuộc khoa khác.');
        }

        if ($classSection->status !== ClassSection::STATUS_OPEN) {
            throw new EnrollmentException('Lớp học phần hiện không mở để đăng ký.');
        }

        $subjectId = $classSection->subject?->id;

        if ($subjectId) {
            $existingSubjectEnrollment = Enrollment::where('student_id', $student->id)
                ->whereHas('classSection', fn ($query) => $query->where('subject_id', $subjectId))
                ->exists();

            if ($existingSubjectEnrollment) {
                throw new EnrollmentException('Bạn đã đăng ký một lớp khác của học phần này.');
            }
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
