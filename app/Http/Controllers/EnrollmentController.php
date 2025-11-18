<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Tuition;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\EnrollmentException;
use App\Services\EnrollmentService;

class EnrollmentController extends Controller
{
    public function __construct(protected EnrollmentService $enrollmentService)
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

        try {
            $this->enrollmentService->enroll($student, $classSection);
        } catch (EnrollmentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

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

}
