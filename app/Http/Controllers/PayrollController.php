<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ClassSection;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\TeachingRate;
use App\Models\ClassSizeCoefficient;
use App\Services\TeachingPaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $semesterId = $request->semester_id;
        $search = trim($request->search ?? '');
        $search = $search === '' ? null : $search;
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $paymentStatus = $request->payment_status;
        $paymentStatus = $paymentStatus === '' ? null : $paymentStatus;

        if ($user->role === 'admin') {
            $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
            $coefficients = ClassSizeCoefficient::all();
            $paymentService = new TeachingPaymentService($base, $coefficients);

            $shouldLoadSections = ($yearId && $semesterId) || $search || $paymentStatus;
            $sections = collect();
            $total = 0;

            if ($shouldLoadSections) {
                $sections = ClassSection::with(['subject', 'teacher.degree', 'courseOffering.semester', 'teachingRate'])
                    ->when($yearId, function ($q) use ($yearId) {
                        $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                            $q->where('academic_year_id', $yearId);
                        });
                    })
                    ->when($semesterId, function ($q) use ($semesterId) {
                        $q->whereHas('courseOffering', function ($q) use ($semesterId) {
                            $q->where('semester_id', $semesterId);
                        });
                    })
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($query) use ($search) {
                            $query->whereHas('teacher', function ($teacherQuery) use ($search) {
                                $teacherQuery->where('teacher_id', 'like', "%$search%")
                                    ->orWhere('first_name', 'like', "%$search%")
                                    ->orWhere('last_name', 'like', "%$search%");
                            })
                                ->orWhere('code', 'like', "%$search%")
                                ->orWhereHas('subject', function ($subjectQuery) use ($search) {
                                    $subjectQuery->where('code', 'like', "%$search%")
                                        ->orWhere('name', 'like', "%$search%");
                                });
                        });
                    })
                    ->when($paymentStatus, function ($q) use ($paymentStatus) {
                        $q->where('payment_status', $paymentStatus);
                    })
                    ->get();

                foreach ($sections as $section) {
                    $section->salary = $paymentService->calculate(
                        $section->teacher,
                        $section->subject,
                        $section->student_count,
                        $section->period_count,
                        optional($section->teachingRate)->amount
                    );
                }

                $total = $sections->sum('salary');
            }

            return view('payrolls.index', [
                'sections' => $sections,
                'academicYears' => $academicYears,
                'semesters' => $semesters,
                'total' => $total,
                'paymentStatuses' => ClassSection::PAYMENT_STATUS_LABELS,
                'shouldPromptFilters' => !$shouldLoadSections,
                'filterApplied' => $shouldLoadSections,
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

        $sections = collect();
        $total = 0;
        $shouldLoadSections = ($yearId && $semesterId) || $search || $paymentStatus;

        if ($shouldLoadSections) {
            $sections = $teacher->classSections()
                ->with(['subject', 'courseOffering.semester', 'teachingRate'])
                ->when($yearId, function ($q) use ($yearId) {
                    $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                        $q->where('academic_year_id', $yearId);
                    });
                })
                ->when($semesterId, function ($q) use ($semesterId) {
                    $q->whereHas('courseOffering', function ($q) use ($semesterId) {
                        $q->where('semester_id', $semesterId);
                    });
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('code', 'like', "%$search%")
                            ->orWhereHas('subject', function ($subjectQuery) use ($search) {
                                $subjectQuery->where('code', 'like', "%$search%")
                                    ->orWhere('name', 'like', "%$search%");
                            })
                            ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                                $teacherQuery->where('teacher_id', 'like', "%$search%")
                                    ->orWhere('first_name', 'like', "%$search%")
                                    ->orWhere('last_name', 'like', "%$search%");
                            });
                    });
                })
                ->when($paymentStatus, function ($q) use ($paymentStatus) {
                    $q->where('payment_status', $paymentStatus);
                })
                ->get();

            foreach ($sections as $section) {
                $section->salary = $paymentService->calculate(
                    $teacher,
                    $section->subject,
                    $section->student_count,
                    $section->period_count,
                    optional($section->teachingRate)->amount
                );
            }

            $total = $sections->sum('salary');
        }

        return view('payrolls.index', [
            'sections' => $sections,
            'teacher' => $teacher,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'total' => $total,
            'paymentStatuses' => ClassSection::PAYMENT_STATUS_LABELS,
            'shouldPromptFilters' => !$shouldLoadSections,
            'filterApplied' => $shouldLoadSections,
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
        $semesterId = $request->semester_id;
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $sections = $teacher->classSections()
            ->with(['subject', 'courseOffering.semester', 'teachingRate'])
            ->when($yearId, function ($q) use ($yearId) {
                $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                    $q->where('academic_year_id', $yearId);
                });
            })
            ->when($semesterId, function ($q) use ($semesterId) {
                $q->whereHas('courseOffering', function ($q) use ($semesterId) {
                    $q->where('semester_id', $semesterId);
                });
            })
            ->get();
        $details = [];
        foreach ($sections as $section) {
            $rate = optional($section->teachingRate)->amount ?? $base;
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
                $section->period_count,
                optional($section->teachingRate)->amount
            );
            $details[] = [
                'section' => $section,
                'base' => $rate,
                'rate' => $rate,
                'degree' => $degree,
                'class' => $classCoef,
                'subject' => $subjectCoef,
                'salary' => $salary,
                'payment_status' => $section->payment_status,
                'payment_status_label' => $section->payment_status_label,
            ];
        }

        $total = $semesterId
            ? $paymentService->calculateForSemester($teacher, $semesterId)
            : collect($details)->sum('salary');

        return view('payrolls.show', [
            'teacher' => $teacher,
            'details' => $details,
            'total' => $total,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'paymentStatuses' => ClassSection::PAYMENT_STATUS_LABELS,
        ]);
    }

    public function updatePaymentStatus(Request $request, ClassSection $classSection)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền cập nhật trạng thái thanh toán.');
        }

        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(array_keys(ClassSection::PAYMENT_STATUS_LABELS))],
        ]);

        $classSection->update($validated);

        return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán thành công.');
    }

    public function exportAll(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $yearId = $request->academic_year_id;
        $semesterId = $request->semester_id;
        $search = $request->search;

        $teachers = Teacher::with(['degree', 'classSections.subject', 'classSections.courseOffering.semester', 'classSections.teachingRate'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('teacher_id', 'like', "%$search%")
                        ->orWhere('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhereHas('classSections', function ($sectionQuery) use ($search) {
                            $sectionQuery->where('code', 'like', "%$search%")
                                ->orWhereHas('subject', function ($subjectQuery) use ($search) {
                                    $subjectQuery->where('code', 'like', "%$search%")
                                        ->orWhere('name', 'like', "%$search%");
                                });
                        });
                });
            })
            ->get();
        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $overallTotal = 0;
        foreach ($teachers as $teacher) {
            $total = 0;
            $sections = $teacher->classSections()
                ->when($yearId, function ($q) use ($yearId) {
                    $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                        $q->where('academic_year_id', $yearId);
                    });
                })
                ->when($semesterId, function ($q) use ($semesterId) {
                    $q->whereHas('courseOffering', function ($q) use ($semesterId) {
                        $q->where('semester_id', $semesterId);
                    });
                })
                ->get();
            foreach ($sections as $section) {
                $total += $paymentService->calculate(
                    $teacher,
                    $section->subject,
                    $section->student_count,
                    $section->period_count,
                    optional($section->teachingRate)->amount
                );
            }
            $teacher->total_salary = $total;
            $overallTotal += $total;
        }

        $pdf = Pdf::loadView('payrolls.list_pdf', [
            'teachers' => $teachers,
            'total' => $overallTotal,
        ])
            ->set_option('defaultFont', 'DejaVu Sans');
        return $pdf->stream('payrolls.pdf');
    }

    public function exportSelected(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $validated = $request->validate([
            'section_ids' => ['required', 'array'],
            'section_ids.*' => ['integer', 'exists:class_sections,id'],
        ]);

        $sections = ClassSection::with(['subject', 'teacher.degree', 'teachingRate'])
            ->whereIn('id', $validated['section_ids'])
            ->get();

        if ($sections->isEmpty()) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Không tìm thấy lớp học phần được chọn.');
        }

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $details = $sections->map(function ($section) use ($paymentService, $coefficients, $base) {
            $teacher = $section->teacher;
            $rate = optional($section->teachingRate)->amount ?? $base;
            $degree = optional($teacher->degree)->coefficient ?? 1;
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
                $section->period_count,
                optional($section->teachingRate)->amount
            );

            return [
                'section' => $section,
                'teacher' => $teacher,
                'base' => $rate,
                'rate' => $rate,
                'degree' => $degree,
                'class' => $classCoef,
                'subject' => $subjectCoef,
                'salary' => $salary,
            ];
        });

        $total = $details->sum('salary');

        $pdf = Pdf::loadView('payrolls.selected_pdf', [
            'details' => $details,
            'total' => $total,
        ])->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->stream('payroll_selected.pdf');
    }

    public function exportDetail(Teacher $teacher, Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $teacher->id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $yearId = $request->academic_year_id;
        $semesterId = $request->semester_id;
        $search = trim($request->search ?? '');
        $search = $search === '' ? null : $search;
        $paymentStatus = $request->payment_status;
        $paymentStatus = $paymentStatus === '' ? null : $paymentStatus;

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $sections = $teacher->classSections()
            ->with(['subject', 'courseOffering.semester', 'teachingRate'])
            ->when($yearId, function ($q) use ($yearId) {
                $q->whereHas('courseOffering.semester', function ($q) use ($yearId) {
                    $q->where('academic_year_id', $yearId);
                });
            })
            ->when($semesterId, function ($q) use ($semesterId) {
                $q->whereHas('courseOffering', function ($q) use ($semesterId) {
                    $q->where('semester_id', $semesterId);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%$search%")
                        ->orWhereHas('subject', function ($subjectQuery) use ($search) {
                            $subjectQuery->where('code', 'like', "%$search%")
                                ->orWhere('name', 'like', "%$search%");
                        })
                        ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                            $teacherQuery->where('teacher_id', 'like', "%$search%")
                                ->orWhere('first_name', 'like', "%$search%")
                                ->orWhere('last_name', 'like', "%$search%");
                        });
                });
            })
            ->when($paymentStatus, function ($q) use ($paymentStatus) {
                $q->where('payment_status', $paymentStatus);
            })
            ->get();
        $details = [];
        foreach ($sections as $section) {
            $rate = optional($section->teachingRate)->amount ?? $base;
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
                $section->period_count,
                optional($section->teachingRate)->amount
            );
            $details[] = [
                'section' => $section,
                'base' => $rate,
                'rate' => $rate,
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

    public function exportTeacherSelected(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $teacher = $user->teacher;
        if (!$teacher) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Không tìm thấy thông tin giáo viên.');
        }

        $validated = $request->validate([
            'section_ids' => ['required', 'array'],
            'section_ids.*' => ['integer', 'exists:class_sections,id'],
        ]);

        $sections = $teacher->classSections()
            ->with(['subject', 'teachingRate'])
            ->whereIn('id', $validated['section_ids'])
            ->get();

        if ($sections->isEmpty()) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Không tìm thấy lớp học phần được chọn.');
        }

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $details = [];
        foreach ($sections as $section) {
            $rate = optional($section->teachingRate)->amount ?? $base;
            $degree = optional($teacher->degree)->coefficient ?? 1;
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
                $section->period_count,
                optional($section->teachingRate)->amount
            );
            $details[] = [
                'section' => $section,
                'base' => $rate,
                'rate' => $rate,
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

        return $pdf->stream('teacher_payroll_' . $teacher->id . '.pdf');
    }

    public function sectionDetail(ClassSection $classSection)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $classSection->teacher_id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền xem bảng lương này.');
        }

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $teacher = $classSection->teacher;
        $rate = optional($classSection->teachingRate)->amount ?? $base;
        $degree = $teacher->degree->coefficient ?? 1;
        $classCoef = optional(
            $coefficients->first(function ($coef) use ($classSection) {
                return $coef->min_students <= $classSection->student_count && $coef->max_students >= $classSection->student_count;
            })
        )->coefficient ?? 1;
        $subjectCoef = $classSection->subject->coefficient ?? 1;

        $salary = $paymentService->calculate(
            $teacher,
            $classSection->subject,
            $classSection->student_count,
            $classSection->period_count,
            optional($classSection->teachingRate)->amount
        );

        return view('payrolls.section', [
            'teacher' => $teacher,
            'section' => $classSection,
            'detail' => [
                'base' => $rate,
                'rate' => $rate,
                'degree' => $degree,
                'class' => $classCoef,
                'subject' => $subjectCoef,
                'salary' => $salary,
            ],
            'paymentStatuses' => ClassSection::PAYMENT_STATUS_LABELS,
        ]);
    }

    public function exportSection(ClassSection $classSection)
    {
        $user = Auth::user();
        if ($user->role === 'teacher' && $user->teacher?->id !== $classSection->teacher_id) {
            return redirect()->route('payrolls.index')
                ->with('error', 'Bạn không có quyền.');
        }

        $base = TeachingRate::orderByDesc('id')->value('amount') ?? 0;
        $coefficients = ClassSizeCoefficient::all();
        $paymentService = new TeachingPaymentService($base, $coefficients);

        $teacher = $classSection->teacher;
        $rate = optional($classSection->teachingRate)->amount ?? $base;
        $degree = $teacher->degree->coefficient ?? 1;
        $classCoef = optional(
            $coefficients->first(function ($coef) use ($classSection) {
                return $coef->min_students <= $classSection->student_count && $coef->max_students >= $classSection->student_count;
            })
        )->coefficient ?? 1;
        $subjectCoef = $classSection->subject->coefficient ?? 1;

        $salary = $paymentService->calculate(
            $teacher,
            $classSection->subject,
            $classSection->student_count,
            $classSection->period_count,
            optional($classSection->teachingRate)->amount
        );

        $pdf = Pdf::loadView('payrolls.section_pdf', [
            'teacher' => $teacher,
            'section' => $classSection,
            'detail' => [
                'base' => $rate,
                'rate' => $rate,
                'degree' => $degree,
                'class' => $classCoef,
                'subject' => $subjectCoef,
                'salary' => $salary,
            ],
        ])->set_option('defaultFont', 'DejaVu Sans');

        return $pdf->stream('section_' . $classSection->id . '.pdf');
    }
}
