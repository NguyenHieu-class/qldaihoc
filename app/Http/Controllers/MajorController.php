<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MajorController extends Controller
{
    /**
     * Hiển thị danh sách ngành học
     */
    public function index(Request $request)
    {
        $query = Major::with('faculty');
        
        // Lọc theo khoa
        if ($request->has('faculty_id') && $request->faculty_id) {
            $query->where('faculty_id', $request->faculty_id);
        }
        
        // Tìm kiếm theo tên hoặc mã ngành
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $majors = $query->paginate(10);
        $faculties = Faculty::all();
        
        return view('majors.index', compact('majors', 'faculties'));
    }

    /**
     * Hiển thị form tạo ngành học mới
     */
    public function create()
    {
        $faculties = Faculty::all();
        return view('majors.create', compact('faculties'));
    }

    /**
     * Lưu ngành học mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:majors',
            'description' => 'nullable|string',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Major::create($request->all());

        return redirect()->route('majors.index')
            ->with('success', 'Ngành học đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của ngành học
     */
    public function show(Major $major)
    {
        $major->load('faculty', 'classes');
        return view('majors.show', compact('major'));
    }

    /**
     * Hiển thị form chỉnh sửa ngành học
     */
    public function edit(Major $major)
    {
        $faculties = Faculty::all();
        return view('majors.edit', compact('major', 'faculties'));
    }

    /**
     * Cập nhật thông tin ngành học trong database
     */
    public function update(Request $request, Major $major)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:majors,code,' . $major->id,
            'description' => 'nullable|string',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $major->update($request->all());

        return redirect()->route('majors.index')
            ->with('success', 'Thông tin ngành học đã được cập nhật thành công.');
    }

    /**
     * Xóa ngành học khỏi database
     */
    public function destroy(Major $major)
    {
        // Kiểm tra xem ngành học có lớp học không trước khi xóa
        if ($major->classes()->count() > 0) {
            return redirect()->route('majors.index')
                ->with('error', 'Không thể xóa ngành học này vì có lớp học liên quan.');
        }

        $major->delete();

        return redirect()->route('majors.index')
            ->with('success', 'Ngành học đã được xóa thành công.');
    }
} 