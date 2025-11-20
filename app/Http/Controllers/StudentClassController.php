<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class StudentClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->isStudent() || !$user->student) {
            return redirect()->route('dashboard.index')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        $student = $user->student;

        $enrollments = Enrollment::with([
            'classSection.subject',
            'classSection.teacher',
            'classSection.courseOffering.semester.academicYear',
        ])->where('student_id', $student->id)
            ->get();

        $activeEnrollments = $enrollments->filter(function ($enrollment) {
            return $enrollment->classSection && $enrollment->classSection->status === ClassSection::STATUS_ACTIVE;
        });

        $completedEnrollments = $enrollments->filter(function ($enrollment) {
            return $enrollment->classSection && $enrollment->classSection->status === ClassSection::STATUS_CLOSED;
        });

        return view('student.classes.index', [
            'student' => $student,
            'activeEnrollments' => $activeEnrollments,
            'completedEnrollments' => $completedEnrollments,
        ]);
    }
}
