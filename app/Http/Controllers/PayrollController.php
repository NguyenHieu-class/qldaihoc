<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ClassSection;
use App\Models\TeachingRate;
use App\Models\ClassSizeCoefficient;
use App\Services\TeachingPaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    protected TeachingPaymentService $paymentService;

    public function __construct(TeachingPaymentService $paymentService)
    {
        $this->middleware(['auth', 'role:admin,teacher']);
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $teachers = Teacher::with(['degree', 'classSections.subject'])->get();

            foreach ($teachers as $teacher) {
                $total = 0;
                foreach ($teacher->classSections as $section) {
                    $total += $this->paymentService->calculate(
                        $teacher,
                        $section->subject,
                        $section->student_count,
                        $section->period_count
                    );
                }
                $teacher->total_salary = $total;
            }

            return view('payrolls.index', [
                'teachers' => $teachers,
            ]);
        }

        $teacher = $user->teacher;
        if (!$teacher) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        $sections = $teacher->classSections()->with('subject')->get();
        foreach ($sections as $section) {
            $section->salary = $this->paymentService->calculate(
                $teacher,
                $section->subject,
                $section->student_count,
                $section->period_count
            );
        }

        return view('payrolls.index', [
            'sections' => $sections,
            'teacher' => $teacher,
        ]);
    }

    public function show(Teacher $teacher)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $teacher->id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền xem bảng lương này.');
        }

        $sections = $teacher->classSections()->with('subject')->get();
        $details = [];
        foreach ($sections as $section) {
            $degree = $teacher->degree->coefficient ?? 1;
            $classCoef = ClassSizeCoefficient::where('min_students', '<=', $section->student_count)
                ->where('max_students', '>=', $section->student_count)
                ->value('coefficient') ?? 1;
            $subjectCoef = $section->subject->coefficient ?? 1;
            $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
            $salary = $this->paymentService->calculate(
                $teacher,
                $section->subject,
                $section->student_count,
                $section->period_count
            );
            $details[] = [
                'section' => $section,
                'base' => $base,
                'degree' => $degree,
                'class' => $classCoef,
                'subject' => $subjectCoef,
                'salary' => $salary,
            ];
        }

        $total = collect($details)->sum('salary');

        return view('payrolls.show', [
            'teacher' => $teacher,
            'details' => $details,
            'total' => $total,
        ]);
    }

    public function exportAll()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $teachers = Teacher::with(['degree', 'classSections.subject'])->get();
        foreach ($teachers as $teacher) {
            $total = 0;
            foreach ($teacher->classSections as $section) {
                $total += $this->paymentService->calculate(
                    $teacher,
                    $section->subject,
                    $section->student_count,
                    $section->period_count
                );
            }
            $teacher->total_salary = $total;
        }

        $pdf = Pdf::loadView('payrolls.list_pdf', ['teachers' => $teachers]);
        return $pdf->stream('payrolls.pdf');
    }

    public function exportDetail(Teacher $teacher)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $teacher->id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $sections = $teacher->classSections()->with('subject')->get();
        $details = [];
        foreach ($sections as $section) {
            $degree = $teacher->degree->coefficient ?? 1;
            $classCoef = ClassSizeCoefficient::where('min_students', '<=', $section->student_count)
                ->where('max_students', '>=', $section->student_count)
                ->value('coefficient') ?? 1;
            $subjectCoef = $section->subject->coefficient ?? 1;
            $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
            $salary = $this->paymentService->calculate(
                $teacher,
                $section->subject,
                $section->student_count,
                $section->period_count
            );
            $details[] = [
                'section' => $section,
                'base' => $base,
                'degree' => $degree,
                'class' => $classCoef,
                'subject' => $subjectCoef,
                'salary' => $salary,
            ];
        }

        $total = collect($details)->sum('salary');

        $pdf = Pdf::loadView('payrolls.detail_pdf', [
            'teacher' => $teacher,
            'details' => $details,
            'total' => $total,
        ]);

        return $pdf->stream('payroll_' . $teacher->id . '.pdf');
    }
}
