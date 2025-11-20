<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Grade;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherClassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return redirect()->route('dashboard.index')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('dashboard.index')->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        $sectionsQuery = ClassSection::with([
            'subject',
            'courseOffering.semester.academicYear',
        ])->where('teacher_id', $teacher->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $sectionsQuery->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhereHas('subject', function ($subjectQuery) use ($search) {
                        $subjectQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('academic_year_id')) {
            $sectionsQuery->whereHas('courseOffering.semester.academicYear', function ($query) use ($request) {
                $query->where('id', $request->input('academic_year_id'));
            });
        }

        if ($request->filled('semester_id')) {
            $sectionsQuery->whereHas('courseOffering', function ($query) use ($request) {
                $query->where('semester_id', $request->input('semester_id'));
            });
        }

        if ($request->filled('status')) {
            $sectionsQuery->where('status', $request->input('status'));
        }

        $sections = $sectionsQuery->orderBy('code')->paginate(10)->withQueryString();

        $academicYears = AcademicYear::with('semesters')->orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderBy('name')->get();
        $statuses = ClassSection::STATUS_LABELS;

        return view('teacher.classes.index', compact('sections', 'academicYears', 'semesters', 'teacher', 'statuses'));
    }

    public function gradebook(ClassSection $classSection)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return redirect()->route('dashboard.index')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $teacher = $user->teacher;

        if (!$teacher || $classSection->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes.index')->with('error', 'Bạn không có quyền truy cập lớp học phần này.');
        }

        $classSection->load(['subject', 'courseOffering.semester.academicYear', 'students.class']);

        $students = $classSection->students->sortBy('student_id')->values();

        $courseOffering = $classSection->courseOffering;
        $semester = $courseOffering ? $courseOffering->semester : null;
        $academicYear = $semester ? $semester->academicYear : null;
        $academicYearNumber = $academicYear ? (int) substr($academicYear->name, 0, 4) : null;

        $grades = collect();

        if ($students->isNotEmpty() && $semester && $academicYearNumber) {
            $grades = Grade::whereIn('student_id', $students->pluck('id'))
                ->where('subject_id', $classSection->subject_id)
                ->where('semester', $semester->name)
                ->where('academic_year', $academicYearNumber)
                ->get()
                ->keyBy('student_id');
        }

        $isClassClosed = $classSection->status === ClassSection::STATUS_CLOSED;

        return view('teacher.classes.gradebook', [
            'classSection' => $classSection,
            'students' => $students,
            'grades' => $grades,
            'teacher' => $teacher,
            'semester' => $semester,
            'academicYear' => $academicYear,
            'academicYearNumber' => $academicYearNumber,
            'isClassClosed' => $isClassClosed,
        ]);
    }

    public function storeGrades(Request $request, ClassSection $classSection)
    {
        $user = Auth::user();

        if (!$user->isTeacher()) {
            return redirect()->route('dashboard.index')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $teacher = $user->teacher;

        if (!$teacher || $classSection->teacher_id !== $teacher->id) {
            return redirect()->route('teacher.classes.index')->with('error', 'Bạn không có quyền truy cập lớp học phần này.');
        }

        if ($classSection->status === ClassSection::STATUS_CLOSED) {
            return redirect()->route('teacher.classes.gradebook', $classSection)
                ->with('error', 'Lớp học phần đã đóng, không thể cập nhật điểm.');
        }

        $courseOffering = $classSection->courseOffering;
        $semester = $courseOffering ? $courseOffering->semester : null;
        $academicYear = $semester ? $semester->academicYear : null;

        if (!$semester || !$academicYear) {
            return redirect()->route('teacher.classes.gradebook', $classSection)
                ->with('error', 'Lớp học phần chưa được cấu hình học kỳ hoặc năm học, không thể nhập điểm.');
        }

        $students = $classSection->students()->orderBy('student_id')->get();
        $studentIds = $students->pluck('id');

        if ($students->isEmpty()) {
            return redirect()->route('teacher.classes.gradebook', $classSection)
                ->with('error', 'Chưa có sinh viên nào trong lớp học phần này để lưu điểm.');
        }

        $academicYearNumber = (int) substr($academicYear->name, 0, 4);

        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.midterm_score' => 'nullable|numeric|min:0|max:10',
            'grades.*.final_score' => 'nullable|numeric|min:0|max:10',
            'grades.*.assignment_score' => 'nullable|numeric|min:0|max:10',
        ]);

        $gradesInput = collect($validated['grades'])->only($studentIds);

        foreach ($students as $student) {
            $scores = $gradesInput->get($student->id, []);

            $grade = Grade::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $classSection->subject_id,
                    'semester' => $semester->name,
                    'academic_year' => $academicYearNumber,
                ],
                [
                    'semester_id' => $semester->id,
                    'midterm_score' => $scores['midterm_score'] ?? null,
                    'final_score' => $scores['final_score'] ?? null,
                    'assignment_score' => $scores['assignment_score'] ?? null,
                ]
            );

            $grade->total_score = $grade->calculateTotalScore();
            $grade->save();
        }

        return redirect()->route('teacher.classes.gradebook', $classSection)
            ->with('success', 'Đã lưu điểm cho tất cả sinh viên trong lớp.');
    }
}
