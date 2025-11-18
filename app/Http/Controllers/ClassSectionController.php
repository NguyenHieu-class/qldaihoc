<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\CourseOffering;
use App\Exceptions\EnrollmentException;
use App\Models\Teacher;
use App\Models\Faculty;
use App\Models\TeachingRate;
use App\Models\Student;
use App\Models\Major;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSectionController extends Controller
{
    public function __construct(protected EnrollmentService $enrollmentService)
    {
    }

    public function index()
    {
        $sections = ClassSection::with(['subject', 'teacher.faculty', 'courseOffering.subject'])->paginate(10);
        return view('class_sections.index', compact('sections'));
    }

    public function create(Request $request)
    {
        $courseOfferingsQuery = CourseOffering::with(['subject', 'semester.academicYear']);
      
        if ($request->filled('faculty_id')) {
            $courseOfferingsQuery->whereHas('subject', function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->filled('academic_year_id')) {
            $courseOfferingsQuery->whereHas('semester', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('semester_id')) {
            $courseOfferingsQuery->where('semester_id', $request->semester_id);
        }

        $courseOfferings = $courseOfferingsQuery->get();

        $teachers = Teacher::with('faculty')
            ->when($request->faculty_id, function ($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            })
            ->get();

        $faculties = Faculty::all();

        $academicYears = \App\Models\AcademicYear::with('semesters')->get();

        $semestersQuery = \App\Models\Semester::with('academicYear');
        if ($request->filled('academic_year_id')) {
            $semestersQuery->where('academic_year_id', $request->academic_year_id);
        }
        $semesters = $semestersQuery->get();

        $teachingRates = TeachingRate::all();

        $statusOptions = ClassSection::STATUS_LABELS;

        return view('class_sections.create', compact('courseOfferings', 'teachers', 'academicYears', 'semesters', 'faculties', 'teachingRates', 'statusOptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:class_sections,code',
            'course_offering_id' => 'required|exists:course_offerings,id',
            'teacher_id' => 'required|exists:teachers,id',
            'teaching_rate_id' => 'required|exists:teaching_rates,id',
            'room' => 'nullable|string',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
            'status' => 'nullable|in:' . implode(',', array_keys(ClassSection::STATUS_LABELS)),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $offering = CourseOffering::find($request->course_offering_id);

        $status = $request->input('status', ClassSection::STATUS_OPEN);

        ClassSection::create([
            'code' => $request->code,
            'course_offering_id' => $offering->id,
            'subject_id' => $offering->subject_id,
            'teacher_id' => $request->teacher_id,
            'teaching_rate_id' => $request->teaching_rate_id,
            'room' => $request->room,
            'period_count' => $request->period_count,
            'student_count' => $request->student_count,
            'status' => $status,
        ]);
        return redirect()->route('class-sections.index')->with('success', 'Đã lưu lớp học phần.');
    }

    public function show(ClassSection $classSection)
    {
        $classSection->load([
            'subject',
            'teacher',
            'teachingRate',
            'courseOffering.semester.academicYear',
            'students' => function ($query) {
                $query->orderBy('student_id');
            },
        ]);

        $courseOffering = $classSection->courseOffering;
        $semester = optional($courseOffering)->semester;
        $academicYear = optional($semester)->academicYear;

        $user = auth()->user();
        $backRoute = null;

        if ($user) {
            if ($user->isAdmin()) {
                $backRoute = route('class-sections.index');
            } elseif ($user->isTeacher()) {
                $backRoute = route('teacher.classes.index');
            } elseif ($user->isStudent()) {
                $backRoute = route('enrollments.index');
            }
        }

        return view('class_sections.show', compact('classSection', 'courseOffering', 'semester', 'academicYear', 'backRoute'));
    }

    public function createStudentSelection(Request $request, ClassSection $classSection)
    {
        $classSection->load(['subject', 'courseOffering.semester.academicYear']);

        $studentsQuery = Student::with('class.major.faculty')
            ->whereDoesntHave('classSections', function ($query) use ($classSection) {
                $query->where('class_section_id', $classSection->id);
            });

        if ($request->filled('search')) {
            $search = $request->input('search');
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('faculty_id')) {
            $studentsQuery->whereHas('class.major', function ($query) use ($request) {
                $query->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->filled('major_id')) {
            $studentsQuery->whereHas('class', function ($query) use ($request) {
                $query->where('major_id', $request->major_id);
            });
        }

        $students = $studentsQuery
            ->orderBy('student_id')
            ->paginate(15)
            ->withQueryString();

        $faculties = Faculty::orderBy('name')->get();
        $majors = Major::with('faculty')->orderBy('name')->get();

        return view('class_sections.select_students', compact('classSection', 'students', 'faculties', 'majors'));
    }

    public function edit(ClassSection $classSection)
    {
        $courseOfferings = CourseOffering::with(['subject', 'semester.academicYear'])->get();
        $teachers = Teacher::with('faculty')->get();
        $teachingRates = TeachingRate::all();
        $statusOptions = ClassSection::STATUS_LABELS;
        return view('class_sections.edit', compact('classSection', 'courseOfferings', 'teachers', 'teachingRates', 'statusOptions'));
    }

    public function update(Request $request, ClassSection $classSection)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:class_sections,code,' . $classSection->id,
            'course_offering_id' => 'required|exists:course_offerings,id',
            'teacher_id' => 'required|exists:teachers,id',
            'teaching_rate_id' => 'required|exists:teaching_rates,id',
            'room' => 'nullable|string',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
            'status' => 'nullable|in:' . implode(',', array_keys(ClassSection::STATUS_LABELS)),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $offering = CourseOffering::find($request->course_offering_id);

        $status = $request->input('status', $classSection->status ?? ClassSection::STATUS_OPEN);

        $classSection->update([
            'code' => $request->code,
            'course_offering_id' => $offering->id,
            'subject_id' => $offering->subject_id,
            'teacher_id' => $request->teacher_id,
            'teaching_rate_id' => $request->teaching_rate_id,
            'room' => $request->room,
            'period_count' => $request->period_count,
            'student_count' => $request->student_count,
            'status' => $status,
        ]);
        return redirect()->route('class-sections.index')->with('success', 'Đã cập nhật lớp học phần.');
    }

    public function destroy(ClassSection $classSection)
    {
        $classSection->delete();
        return redirect()->route('class-sections.index')->with('success', 'Đã xóa lớp học phần.');
    }

    public function addStudent(Request $request, ClassSection $classSection): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ], [
            'student_ids.required' => 'Vui lòng chọn ít nhất một sinh viên.',
            'student_ids.array' => 'Danh sách sinh viên không hợp lệ.',
        ]);

        $students = Student::whereIn('id', $data['student_ids'])->get();
        $successCount = 0;
        $errors = [];

        foreach ($students as $student) {
            try {
                $this->enrollmentService->enroll($student, $classSection);
                $successCount++;
            } catch (EnrollmentException $exception) {
                $errors[] = $student->student_id . ': ' . $exception->getMessage();
            }
        }

        $response = [];
        if ($successCount > 0) {
            $response['success'] = "Đã thêm {$successCount} sinh viên vào lớp học phần.";
        }

        if (!empty($errors)) {
            $response['error'] = implode(' ', $errors);
        }

        return back()->with($response);
    }

    /**
     * Sinh mã lớp học phần tự động và lưu.
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_offering_id' => 'required|exists:course_offerings,id',
            'teacher_id' => 'required|exists:teachers,id',
            'teaching_rate_id' => 'required|exists:teaching_rates,id',
            'number_of_sections' => 'required|integer|min:1',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
            'status' => 'nullable|in:' . implode(',', array_keys(ClassSection::STATUS_LABELS)),
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $offering = CourseOffering::with('subject')->find($request->course_offering_id);
        $subject = $offering->subject;
        $prefix = $subject->code . 'N';
        $last = ClassSection::where('code', 'like', $prefix.'%')
            ->orderBy('code', 'desc')
            ->first();
        $num = 1;
        if ($last) {
            $num = intval(substr($last->code, strlen($prefix))) + 1;
        }
        $createdCodes = [];
        $status = $request->input('status', ClassSection::STATUS_OPEN);

        for ($i = 0; $i < $request->number_of_sections; $i++) {
            $code = $prefix . str_pad($num + $i, 2, '0', STR_PAD_LEFT);

            ClassSection::create([
                'code' => $code,
                'course_offering_id' => $offering->id,
                'subject_id' => $offering->subject_id,
                'teacher_id' => $request->teacher_id,
                'teaching_rate_id' => $request->teaching_rate_id,
                'room' => $request->room,
                'period_count' => $request->period_count,
                'student_count' => $request->student_count,
                'status' => $status,
            ]);

            $createdCodes[] = $code;
        }

        return redirect()->route('class-sections.index')
            ->with('success', 'Đã tạo lớp học phần: ' . implode(', ', $createdCodes));
    }
}
