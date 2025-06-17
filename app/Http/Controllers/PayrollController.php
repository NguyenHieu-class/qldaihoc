<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\TeachingRate;
use App\Models\ClassSizeCoefficient;
use App\Services\TeachingPaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,teacher']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $yearId = $request->academic_year_id;
        $academicYears = AcademicYear::all();

        if ($user->role === 'admin') {
            $teachers = Teacher::with('degree')->get();
            $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
            $coefficients = ClassSizeCoefficient::all();
            $paymentService = new TeachingPaymentService($base, $coefficients);

            foreach ($teachers as $teacher) {
                $total = 0;
                $sections = $teacher->classSections()
                    ->with(['subject', 'courseOffering.semester'])
                    ->when($yearId, function ($q) use ($yearId) {
                        $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                            $q->where('academic_year_id', $yearId);
                        });
                    })
                    ->get();

                foreach ($sections as $section) {
                    $total += $paymentService->calculate(
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
                'academicYears' => $academicYears,
            ]);
        }

        $teacher = $user->teacher;
        if (!$teacher) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $sections = $teacher->classSections()
            ->with(['subject', 'courseOffering.semester'])
            ->when($yearId, function ($q) use ($yearId) {
                $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                    $q->where('academic_year_id', $yearId);
                });
            })
            ->get();
        foreach ($sections as $section) {
            $section->salary = $paymentService->calculate(
                $teacher,
                $section->subject,
                $section->student_count,
                $section->period_count
            );
        }

        return view('payrolls.index', [
            'sections' => $sections,
            'teacher' => $teacher,
            'academicYears' => $academicYears,
        ]);
    }

    public function show(Teacher $teacher, Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $teacher->id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền xem bảng lương này.');
        }

        $yearId = $request->academic_year_id;
        $academicYears = AcademicYear::all();

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $sections = $teacher->classSections()
            ->with(['subject', 'courseOffering.semester'])
            ->when($yearId, function ($q) use ($yearId) {
                $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                    $q->where('academic_year_id', $yearId);
                });
            })
            ->get();
        $details = [];
        foreach ($sections as $section) {
            $degree = $teacher->degree->coefficient ?? 1;
            $classCoef = optional(
                $coefficients->first(function ($coef) use ($section) {
                    return $coef->min_students <= $section->student_count && $coef->max_students >= $section->student_count;
                })
            )->coefficient ?? 1;
            $subjectCoef = $section->subject->coefficient ?? 1;
            $salary = $paymentService->calculate(
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
            'academicYears' => $academicYears,
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
        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        foreach ($teachers as $teacher) {
            $total = 0;
            foreach ($teacher->classSections as $section) {
                $total += $paymentService->calculate(
                    $teacher,
                    $section->subject,
                    $section->student_count,
                    $section->period_count
                );
            }
            $teacher->total_salary = $total;
        }

        $pdf = Pdf::loadView('payrolls.list_pdf', ['teachers' => $teachers])
            ->set_option('defaultFont', 'DejaVu Sans');
        return $pdf->stream('payrolls.pdf');
    }

    public function exportDetail(Teacher $teacher)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $teacher->id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $sections = $teacher->classSections()->with('subject')->get();
        $details = [];
        foreach ($sections as $section) {
            $degree = $teacher->degree->coefficient ?? 1;
            $classCoef = optional(
                $coefficients->first(function ($coef) use ($section) {
                    return $coef->min_students <= $section->student_count && $coef->max_students >= $section->student_count;
                })
            )->coefficient ?? 1;
            $subjectCoef = $section->subject->coefficient ?? 1;
            $salary = $paymentService->calculate(
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
        ])->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->stream('payroll_' . $teacher->id . '.pdf');
    }
}
