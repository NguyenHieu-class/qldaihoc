<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    /**
     * Hiển thị danh sách điểm số
     */
    public function index(Request $request)
    {
        $query = Grade::with(['student.class.major.faculty', 'subject']);
        
        // Lọc theo sinh viên
        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }
        
        // Lọc theo môn học
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Lọc theo học kỳ
        if ($request->has('semester_id') && $request->semester_id) {
            $query->where('semester_id', $request->semester_id);
        }
        
        // Lọc theo lớp học
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        // Tìm kiếm theo tên sinh viên hoặc mã sinh viên
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        $grades = $query->paginate(10);
        $students = Student::with('class')->get();
        $subjects = Subject::all();
        $classes = \App\Models\Classes::with('major.faculty')->get();
        $semesters = Semester::with('academicYear')->get();

        return view('grades.index', compact('grades', 'students', 'subjects', 'classes', 'semesters'));
    }

    /**
     * Hiển thị form tạo điểm số mới
     */
    public function create()
    {
        $students = Student::with('class')->get();
        $subjects = Subject::all();
        $semesters = Semester::with('academicYear')->get();

        return view('grades.create', compact('students', 'subjects', 'semesters'));
    }

    /**
     * Lưu điểm số mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'midterm_score' => 'nullable|numeric|min:0|max:10',
            'final_score' => 'nullable|numeric|min:0|max:10',
            'assignment_score' => 'nullable|numeric|min:0|max:10',
            'semester_id' => 'required|exists:semesters,id',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $semester = Semester::with('academicYear')->find($request->semester_id);
        $year = (int) substr($semester->academicYear->name, 0, 4);

        // Kiểm tra xem đã có điểm cho sinh viên, môn học, học kỳ và năm học này chưa
        $existingGrade = Grade::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('semester', $semester->name)
            ->where('academic_year', $year)
            ->first();
            
        if ($existingGrade) {
            return redirect()->back()
                ->with('error', 'Đã tồn tại điểm cho sinh viên, môn học, học kỳ và năm học này.')
                ->withInput();
        }

        // Tạo điểm mới
        $data = $request->all();
        $data['semester_id'] = $semester->id;
        $data['semester'] = $semester->name;
        $data['academic_year'] = $year;

        $grade = new Grade($data);
        
        // Tính điểm tổng kết
        $grade->total_score = $grade->calculateTotalScore();
        $grade->save();

        return redirect()->route('grades.index')
            ->with('success', 'Điểm đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của điểm số
     */
    public function show(Grade $grade)
    {
        $grade->load(['student.class', 'subject']);
        return view('grades.show', compact('grade'));
    }

    /**
     * Hiển thị form chỉnh sửa điểm số
     */
    public function edit(Grade $grade)
    {
        $students = Student::with('class')->get();
        $subjects = Subject::all();
        $semesters = Semester::with('academicYear')->get();

        return view('grades.edit', compact('grade', 'students', 'subjects', 'semesters'));
    }

    /**
     * Cập nhật thông tin điểm số trong database
     */
    public function update(Request $request, Grade $grade)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'midterm_score' => 'nullable|numeric|min:0|max:10',
            'final_score' => 'nullable|numeric|min:0|max:10',
            'assignment_score' => 'nullable|numeric|min:0|max:10',
            'semester_id' => 'required|exists:semesters,id',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $semester = Semester::with('academicYear')->find($request->semester_id);
        $year = (int) substr($semester->academicYear->name, 0, 4);

        // Kiểm tra xem đã có điểm cho sinh viên, môn học, học kỳ và năm học này chưa (trừ điểm hiện tại)
        $existingGrade = Grade::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('semester', $semester->name)
            ->where('academic_year', $year)
            ->where('id', '!=', $grade->id)
            ->first();
            
        if ($existingGrade) {
            return redirect()->back()
                ->with('error', 'Đã tồn tại điểm cho sinh viên, môn học, học kỳ và năm học này.')
                ->withInput();
        }

        // Cập nhật điểm
        $data = $request->all();
        $data['semester_id'] = $semester->id;
        $data['semester'] = $semester->name;
        $data['academic_year'] = $year;

        $grade->update($data);
        
        // Tính lại điểm tổng kết
        $grade->updateTotalScore();

        return redirect()->route('grades.index')
            ->with('success', 'Điểm đã được cập nhật thành công.');
    }

    /**
     * Xóa điểm số khỏi database
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('grades.index')
            ->with('success', 'Điểm đã được xóa thành công.');
    }
    
    /**
     * Hiển thị bảng điểm của sinh viên
     */
    public function studentTranscript(Student $student)
    {
        $student->load(['grades.subject', 'class.major.faculty']);
        
        // Nhóm điểm theo năm học và học kỳ
        $groupedGrades = $student->grades->groupBy(['academic_year', 'semester']);
        
        return view('grades.transcript', compact('student', 'groupedGrades'));
    }
} 