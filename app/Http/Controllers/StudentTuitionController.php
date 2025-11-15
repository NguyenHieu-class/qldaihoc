<?php

namespace App\Http\Controllers;

use App\Models\Tuition;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentTuitionController extends Controller
{
    /**
     * Hiển thị các khoản học phí của sinh viên đăng nhập.
     */
    public function index(): View
    {
        $student = Auth::user()->student;

        abort_unless($student, 404);

        $tuitions = $student->tuitions()
            ->with('semester.academicYear')
            ->orderByDesc('due_date')
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'pending' => $tuitions->where('status', Tuition::STATUS_PENDING)->sum('amount'),
            'paid' => $tuitions->where('status', Tuition::STATUS_PAID)->sum('amount'),
            'overdue' => $tuitions->where('status', Tuition::STATUS_OVERDUE)->sum('amount'),
        ];

        return view('student.tuitions.index', [
            'student' => $student,
            'tuitions' => $tuitions,
            'summary' => $summary,
        ]);
    }
}
