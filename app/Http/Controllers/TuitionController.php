<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Student;
use App\Models\Tuition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TuitionController extends Controller
{
    /**
     * Hiển thị danh sách học phí.
     */
    public function index(Request $request): View
    {
        $tuitionsQuery = Tuition::with(['student.class.major.faculty', 'semester.academicYear'])
            ->orderByDesc('due_date')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $tuitionsQuery->where('status', $request->string('status'));
        }

        if ($request->filled('semester_id')) {
            $tuitionsQuery->where('semester_id', $request->integer('semester_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $tuitionsQuery->whereHas('student', function ($query) use ($search) {
                $query->where('student_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $tuitions = $tuitionsQuery->paginate(15)->withQueryString();

        return view('tuitions.index', [
            'tuitions' => $tuitions,
            'statuses' => Tuition::statuses(),
            'semesters' => Semester::with('academicYear')->orderBy('name')->get(),
        ]);
    }

    /**
     * Form tạo mới.
     */
    public function create(): View
    {
        return view('tuitions.create', [
            'tuition' => new Tuition(),
            'students' => Student::with('class.major.faculty')->orderBy('student_id')->get(),
            'semesters' => Semester::with('academicYear')->orderBy('name')->get(),
            'statuses' => Tuition::statuses(),
        ]);
    }

    /**
     * Lưu khoản học phí mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        if ($validated['status'] !== Tuition::STATUS_PAID) {
            $validated['paid_at'] = null;
        } elseif (empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        }

        Tuition::create($validated);

        return redirect()->route('tuitions.index')->with('success', 'Tạo khoản học phí thành công.');
    }

    /**
     * Form chỉnh sửa.
     */
    public function edit(Tuition $tuition): View
    {
        return view('tuitions.edit', [
            'tuition' => $tuition->load(['student', 'semester']),
            'students' => Student::with('class.major.faculty')->orderBy('student_id')->get(),
            'semesters' => Semester::with('academicYear')->orderBy('name')->get(),
            'statuses' => Tuition::statuses(),
        ]);
    }

    /**
     * Cập nhật khoản học phí.
     */
    public function update(Request $request, Tuition $tuition): RedirectResponse
    {
        $validated = $this->validateData($request);

        if ($validated['status'] !== Tuition::STATUS_PAID) {
            $validated['paid_at'] = null;
        } elseif (empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        }

        $tuition->update($validated);

        return redirect()->route('tuitions.index')->with('success', 'Cập nhật khoản học phí thành công.');
    }

    /**
     * Xoá khoản học phí.
     */
    public function destroy(Tuition $tuition): RedirectResponse
    {
        $tuition->delete();

        return redirect()->route('tuitions.index')->with('success', 'Đã xoá khoản học phí.');
    }

    /**
     * Xử lý validate dữ liệu chung.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(array_keys(Tuition::statuses()))],
            'due_date' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
