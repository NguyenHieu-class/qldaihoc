<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSection;
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

        $sections = $sectionsQuery->orderBy('code')->paginate(10)->withQueryString();

        $academicYears = AcademicYear::with('semesters')->orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderBy('name')->get();

        return view('teacher.classes.index', compact('sections', 'academicYears', 'semesters', 'teacher'));
    }
}
