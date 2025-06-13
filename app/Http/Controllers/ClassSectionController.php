<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSectionController extends Controller
{
    public function index()
    {
        $sections = ClassSection::with(['subject', 'teacher.faculty'])->paginate(10);
        return view('class_sections.index', compact('sections'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $teachers = Teacher::with('faculty')->get();
        return view('class_sections.create', compact('subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:class_sections,code',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room' => 'nullable|string',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        ClassSection::create($request->all());
        return redirect()->route('class-sections.index')->with('success', 'Đã lưu lớp học phần.');
    }

    public function edit(ClassSection $classSection)
    {
        $subjects = Subject::all();
        $teachers = Teacher::with('faculty')->get();
        return view('class_sections.edit', compact('classSection', 'subjects', 'teachers'));
    }

    public function update(Request $request, ClassSection $classSection)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:class_sections,code,' . $classSection->id,
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room' => 'nullable|string',
            'period_count' => 'required|integer|min:0',
            'student_count' => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $classSection->update($request->all());
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
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $subject = Subject::find($request->subject_id);
        $prefix = $subject->code . 'N';
        $last = ClassSection::where('code', 'like', $prefix.'%')
            ->orderBy('code', 'desc')
            ->first();
        $num = 1;
        if ($last) {
            $num = intval(substr($last->code, strlen($prefix))) + 1;
        }
        $code = $prefix . str_pad($num, 2, '0', STR_PAD_LEFT);

        ClassSection::create([
            'code' => $code,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'room' => $request->room,
            'period_count' => $request->period_count ?? 0,
            'student_count' => $request->student_count ?? 0,
        ]);

        return redirect()->route('class-sections.index')->with('success', 'Đã tạo lớp học phần ' . $code);
    }
}
