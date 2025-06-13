<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Hiển thị danh sách giáo viên
     */
    public function index(Request $request)
    {
        $query = Teacher::with('faculty');
        
        // Lọc theo khoa
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('faculty_id', $request->faculty_id);
        }
        
        // Tìm kiếm theo tên, mã giáo viên hoặc email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('teacher_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $teachers = $query->paginate(10);
        $faculties = Faculty::all();
        
        return view('teachers.index', compact('teachers', 'faculties'));
    }

    /**
     * Hiển thị form tạo giáo viên mới
     */
    public function create()
    {
        $faculties = Faculty::all();
        return view('teachers.create', compact('faculties'));
    }

    /**
     * Lưu giáo viên mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|string|max:50|unique:teachers',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:teachers',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'faculty_id' => 'required|exists:faculties,id',
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
                    'role' => 'teacher',
                ]);
                
                $userData = $user->id;
            }
            
            // Tạo giáo viên
            $teacherData = $request->except('create_account', 'password');
            if ($userData) {
                $teacherData['user_id'] = $userData;
            }
            
            Teacher::create($teacherData);
            
            DB::commit();
            
            return redirect()->route('teachers.index')
                ->with('success', 'Giáo viên đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi tạo giáo viên: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết của giáo viên
     */
    public function show(Teacher $teacher)
    {
        $teacher->load('faculty');
        return view('teachers.show', compact('teacher'));
    }

    /**
     * Hiển thị form chỉnh sửa giáo viên
     */
    public function edit(Teacher $teacher)
    {
        $faculties = Faculty::all();
        return view('teachers.edit', compact('teacher', 'faculties'));
    }

    /**
     * Cập nhật thông tin giáo viên trong database
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|string|max:50|unique:teachers,teacher_id,' . $teacher->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $teacher->update($request->all());

        // Cập nhật thông tin người dùng nếu có
        if ($teacher->user_id) {
            $teacher->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('teachers.index')
            ->with('success', 'Thông tin giáo viên đã được cập nhật thành công.');
    }

    /**
     * Xóa giáo viên khỏi database
     */
    public function destroy(Teacher $teacher)
    {
        DB::beginTransaction();

        try {
            // Xóa tài khoản người dùng nếu có
            if ($teacher->user_id) {
                $teacher->user->delete();
            }
            
            // Xóa giáo viên
            $teacher->delete();
            
            DB::commit();
            
            return redirect()->route('teachers.index')
                ->with('success', 'Giáo viên đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xóa giáo viên: ' . $e->getMessage());
        }
    }
} 