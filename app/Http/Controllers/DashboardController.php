<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tạo một instance mới của controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị dashboard cho admin
     */
    public function admin()
    {
        // Kiểm tra quyền admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Đếm số lượng các đối tượng
        $facultyCount = Faculty::count();
        $majorCount = Major::count();
        $classCount = Classes::count();
        $studentCount = Student::count();
        $teacherCount = Teacher::count();
        $subjectCount = Subject::count();

        // Lấy danh sách sinh viên mới nhất
        $recentStudents = Student::with('class.major.faculty')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Lấy danh sách giáo viên mới nhất
        $recentTeachers = Teacher::with('faculty')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'facultyCount',
            'majorCount',
            'classCount',
            'studentCount',
            'teacherCount',
            'subjectCount',
            'recentStudents',
            'recentTeachers'
        ));
    }

    /**
     * Hiển thị dashboard cho giáo viên
     */
    public function teacher()
    {
        // Kiểm tra quyền giáo viên
        if (!Auth::user()->isTeacher()) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Lấy thông tin giáo viên
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        // Lấy thông tin khoa của giáo viên
        $faculty = $teacher->faculty;
        
        // Đếm số lượng sinh viên trong khoa
        $studentCount = Student::whereHas('class.major', function ($query) use ($faculty) {
            $query->where('faculty_id', $faculty->id);
        })->count();
        
        // Đếm số lượng lớp học trong khoa
        $classCount = Classes::whereHas('major', function ($query) use ($faculty) {
            $query->where('faculty_id', $faculty->id);
        })->count();
        
        // Đếm số lượng ngành học trong khoa
        $majorCount = Major::where('faculty_id', $faculty->id)->count();

        return view('dashboard.teacher', compact(
            'teacher',
            'faculty',
            'studentCount',
            'classCount',
            'majorCount'
        ));
    }

    /**
     * Hiển thị dashboard cho sinh viên
     */
    public function student()
    {
        // Kiểm tra quyền sinh viên
        if (!Auth::user()->isStudent()) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Lấy thông tin sinh viên
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Không tìm thấy thông tin sinh viên.');
        }

        // Lấy thông tin lớp, ngành, khoa của sinh viên
        $student->load('class.major.faculty');
        
        // Lấy điểm số của sinh viên
        $grades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->get();
        
        // Tính điểm trung bình
        $averageScore = 0;
        $totalCredits = 0;
        
        foreach ($grades as $grade) {
            if ($grade->total_score !== null) {
                $credits = $grade->subject->credits;
                $averageScore += $grade->total_score * $credits;
                $totalCredits += $credits;
            }
        }
        
        if ($totalCredits > 0) {
            $averageScore = $averageScore / $totalCredits;
        }

        return view('dashboard.student', compact(
            'student',
            'grades',
            'averageScore'
        ));
    }

    /**
     * Chuyển hướng người dùng đến dashboard tương ứng
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('dashboard.admin');
        } elseif (Auth::user()->isTeacher()) {
            return redirect()->route('dashboard.teacher');
        } else {
            return redirect()->route('dashboard.student');
        }
    }
} 