<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            ->whereHas('courseOffering', function ($q) use ($semesterIds) {
                if (count($semesterIds)) {
                    $q->whereIn('semester_id', $semesterIds);
                }
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

        if ($classSection->students()->count() >= $classSection->student_count) {
            return back()->with('error', 'Lớp đã đủ số lượng.');
        }

        Enrollment::create([
            'student_id' => $student->id,
            'class_section_id' => $classSection->id,
        ]);

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
        $enrollment->delete();
        return back()->with('success', 'Đã hủy đăng ký.');
    }
}
