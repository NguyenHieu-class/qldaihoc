<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\CourseOffering;
use App\Models\Teacher;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSectionController extends Controller
{
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

        return view('class_sections.create', compact('courseOfferings', 'teachers', 'academicYears', 'semesters', 'faculties'));

        return view('class_sections.create', compact('courseOfferings', 'teachers', 'academicYears', 'semesters'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:class_sections,code',
            'course_offering_id' => 'required|exists:course_offerings,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room' => 'nullable|string',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $offering = CourseOffering::find($request->course_offering_id);

        ClassSection::create([
            'code' => $request->code,
            'course_offering_id' => $offering->id,
            'subject_id' => $offering->subject_id,
            'teacher_id' => $request->teacher_id,
            'room' => $request->room,
            'period_count' => $request->period_count,
            'student_count' => $request->student_count,
        ]);
        return redirect()->route('class-sections.index')->with('success', 'Đã lưu lớp học phần.');
    }

    public function edit(ClassSection $classSection)
    {
        $courseOfferings = CourseOffering::with(['subject', 'semester.academicYear'])->get();
        $teachers = Teacher::with('faculty')->get();
        return view('class_sections.edit', compact('classSection', 'courseOfferings', 'teachers'));
    }

    public function update(Request $request, ClassSection $classSection)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:class_sections,code,' . $classSection->id,
            'course_offering_id' => 'required|exists:course_offerings,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room' => 'nullable|string',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $offering = CourseOffering::find($request->course_offering_id);

        $classSection->update([
            'code' => $request->code,
            'course_offering_id' => $offering->id,
            'subject_id' => $offering->subject_id,
            'teacher_id' => $request->teacher_id,
            'room' => $request->room,
            'period_count' => $request->period_count,
            'student_count' => $request->student_count,
        ]);
        return redirect()->route('class-sections.index')->with('success', 'Đã cập nhật lớp học phần.');
    }

    public function destroy(ClassSection $classSection)
    {
        $classSection->delete();
        return redirect()->route('class-sections.index')->with('success', 'Đã xóa lớp học phần.');
    }

    /**
     * Sinh mã lớp học phần tự động và lưu.
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_offering_id' => 'required|exists:course_offerings,id',
            'teacher_id' => 'required|exists:teachers,id',
            'number_of_sections' => 'required|integer|min:1',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
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
        for ($i = 0; $i < $request->number_of_sections; $i++) {
            $code = $prefix . str_pad($num + $i, 2, '0', STR_PAD_LEFT);

            ClassSection::create([
                'code' => $code,
                'course_offering_id' => $offering->id,
                'subject_id' => $offering->subject_id,
                'teacher_id' => $request->teacher_id,
                'room' => $request->room,
                'period_count' => $request->period_count,
                'student_count' => $request->student_count,
            ]);

            $createdCodes[] = $code;
        }

        return redirect()->route('class-sections.index')
            ->with('success', 'Đã tạo lớp học phần: ' . implode(', ', $createdCodes));
    }
}
