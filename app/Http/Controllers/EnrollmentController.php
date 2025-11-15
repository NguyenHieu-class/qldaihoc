<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Tuition;
use App\Models\TuitionSetting;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
    }

    /**
     * Danh sách lớp có thể đăng ký và lớp đã đăng ký.
     */
    public function index()
    {
        $student = auth()->user()->student;

        $latestYear = AcademicYear::orderBy('id', 'desc')->first();
        $semesterIds = [];
        if ($latestYear) {
            $semesterIds = Semester::where('academic_year_id', $latestYear->id)->pluck('id');
        }

        $registeredIds = $student->classSections()->pluck('class_sections.id');

        $availableSections = ClassSection::with(['subject', 'teacher', 'courseOffering.semester.academicYear'])
            ->where('status', ClassSection::STATUS_OPEN)
            ->whereHas('courseOffering', function ($q) use ($semesterIds) {
                if (count($semesterIds)) {
                    $q->whereIn('semester_id', $semesterIds);
                }
                $q->where('status', CourseOffering::STATUS_OPEN);
            })
            ->whereNotIn('id', $registeredIds)
            ->get();

        $registrations = Enrollment::with('classSection.subject', 'classSection.teacher', 'classSection.courseOffering.semester.academicYear')
            ->where('student_id', $student->id)
            ->get();

        return view('enrollments.index', compact('availableSections', 'registrations'));
    }

    /**
     * Đăng ký lớp học phần.
     */
    public function store(ClassSection $classSection): RedirectResponse
    {
        $student = auth()->user()->student;

        if ($classSection->students()->where('enrollments.student_id', $student->id)->exists()) {
            return back()->with('error', 'Bạn đã đăng ký lớp này.');
        }

        $classSection->loadMissing('courseOffering.semester', 'subject');

        if ($classSection->status !== ClassSection::STATUS_OPEN) {
            return back()->with('error', 'Lớp học phần hiện không mở để đăng ký.');
        }

        if ($classSection->courseOffering && $classSection->courseOffering->status !== CourseOffering::STATUS_OPEN) {
            return back()->with('error', 'Môn học này hiện không mở để đăng ký.');
        }

        if ($classSection->students()->count() >= $classSection->student_count) {
            return back()->with('error', 'Lớp đã đủ số lượng.');
        }

        DB::transaction(function () use ($student, $classSection) {
            Enrollment::create([
                'student_id' => $student->id,
                'class_section_id' => $classSection->id,
            ]);

            $this->createTuitionForEnrollment($student, $classSection);
        });

        return back()->with('success', 'Đăng ký thành công.');
    }

    /**
     * Hủy đăng ký lớp học phần.
     */
    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $student = auth()->user()->student;
        if ($enrollment->student_id != $student->id) {
            abort(403);
        }
        $enrollment->loadMissing('classSection');

        $classSection = $enrollment->classSection;

        if ($classSection && in_array($classSection->status, [ClassSection::STATUS_ACTIVE, ClassSection::STATUS_CLOSED], true)) {
            return back()->with('error', 'Không thể hủy đăng ký lớp học phần đã đang hoạt động hoặc đã đóng.');
        }

        DB::transaction(function () use ($enrollment, $student) {
            Tuition::where('student_id', $student->id)
                ->where('class_section_id', $enrollment->class_section_id)
                ->where('status', Tuition::STATUS_PENDING)
                ->delete();

            $enrollment->delete();
        });
        return back()->with('success', 'Đã hủy đăng ký.');
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
