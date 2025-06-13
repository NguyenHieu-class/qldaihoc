<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Hiển thị danh sách sinh viên
     */
    public function index(Request $request)
    {
        $query = Student::with('class.major.faculty');
        
        // Lọc theo lớp
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        // Lọc theo ngành học
        if ($request->has('major_id') && $request->major_id) {
            $query->whereHas('class', function($q) use ($request) {
                $q->where('major_id', $request->major_id);
            });
        }
        
        // Lọc theo khoa
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->whereHas('class.major', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }
        
        // Tìm kiếm theo tên, mã sinh viên hoặc email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $students = $query->paginate(10);
        $classes = Classes::with('major.faculty')->get();
        $majors = \App\Models\Major::with('faculty')->get();
        $faculties = \App\Models\Faculty::all();
        
        return view('students.index', compact('students', 'classes', 'majors', 'faculties'));
    }

    /**
     * Hiển thị form tạo sinh viên mới
     */
    public function create()
    {
        $classes = Classes::with('major.faculty')->get();
        return view('students.create', compact('classes'));
    }

    /**
     * Lưu sinh viên mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:50|unique:students',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:students',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'create_account' => 'boolean',
            'password' => 'required_if:create_account,1|nullable|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $userData = null;
            
            // Tạo tài khoản người dùng nếu được yêu cầu
            if ($request->create_account) {
                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'student',
                ]);
                
                $userData = $user->id;
            }
            
            // Tạo sinh viên
            $studentData = $request->except('create_account', 'password');
            if ($userData) {
                $studentData['user_id'] = $userData;
            }
            
            Student::create($studentData);
            
            DB::commit();
            
            return redirect()->route('students.index')
                ->with('success', 'Sinh viên đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi tạo sinh viên: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết của sinh viên
     */
    public function show(Student $student)
    {
        $student->load('class.major.faculty', 'grades.subject');
        return view('students.show', compact('student'));
    }

    /**
     * Hiển thị form chỉnh sửa sinh viên
     */
    public function edit(Student $student)
    {
        $classes = Classes::with('major.faculty')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Cập nhật thông tin sinh viên trong database
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:50|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->all());

        // Cập nhật thông tin người dùng nếu có
        if ($student->user_id) {
            $student->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', 'Thông tin sinh viên đã được cập nhật thành công.');
    }

    /**
     * Xóa sinh viên khỏi database
     */
    public function destroy(Student $student)
    {
        DB::beginTransaction();

        try {
            // Xóa tài khoản người dùng nếu có
            if ($student->user_id) {
                $student->user->delete();
            }
            
            // Xóa sinh viên
            $student->delete();
            
            DB::commit();
            
            return redirect()->route('students.index')
                ->with('success', 'Sinh viên đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xóa sinh viên: ' . $e->getMessage());
        }
    }
} 