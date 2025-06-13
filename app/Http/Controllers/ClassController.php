<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    /**
     * Hiển thị danh sách lớp học
     */
    public function index(Request $request)
    {
        $query = Classes::with('major.faculty');
        
        // Lọc theo ngành học
        if ($request->has('major_id') && $request->major_id) {
            $query->where('major_id', $request->major_id);
        }
        
        // Lọc theo khoa (thông qua ngành học)
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->whereHas('major', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }
        
        // Lọc theo năm học
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }
        
        // Tìm kiếm theo tên hoặc mã lớp
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $classes = $query->paginate(10);
        $majors = Major::with('faculty')->get();
        $faculties = \App\Models\Faculty::all();
        $years = Classes::distinct()->orderBy('year', 'desc')->pluck('year');
        
        return view('classes.index', compact('classes', 'majors', 'faculties', 'years'));
    }

    /**
     * Hiển thị form tạo lớp học mới
     */
    public function create()
    {
        $majors = Major::with('faculty')->get();
        return view('classes.create', compact('majors'));
    }

    /**
     * Lưu lớp học mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:classes',
            'major_id' => 'required|exists:majors,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 10),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Classes::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của lớp học
     */
    public function show(Classes $class)
    {
        $class->load('major.faculty', 'students');
        return view('classes.show', compact('class'));
    }

    /**
     * Hiển thị form chỉnh sửa lớp học
     */
    public function edit(Classes $class)
    {
        $majors = Major::with('faculty')->get();
        return view('classes.edit', compact('class', 'majors'));
    }

    /**
     * Cập nhật thông tin lớp học trong database
     */
    public function update(Request $request, Classes $class)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:classes,code,' . $class->id,
            'major_id' => 'required|exists:majors,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 10),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Thông tin lớp học đã được cập nhật thành công.');
    }

    /**
     * Xóa lớp học khỏi database
     */
    public function destroy(Classes $class)
    {
        // Kiểm tra xem lớp học có sinh viên không trước khi xóa
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Không thể xóa lớp học này vì có sinh viên liên quan.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được xóa thành công.');
    }
} 