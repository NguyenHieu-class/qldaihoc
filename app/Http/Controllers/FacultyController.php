<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacultyController extends Controller
{
    /**
     * Hiển thị danh sách khoa
     */
    public function index()
    {
        $faculties = Faculty::paginate(10);
        return view('faculties.index', compact('faculties'));
    }

    /**
     * Hiển thị form tạo khoa mới
     */
    public function create()
    {
        return view('faculties.create');
    }

    /**
     * Lưu khoa mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:faculties',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Faculty::create($request->all());

        return redirect()->route('faculties.index')
            ->with('success', 'Khoa đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của khoa
     */
    public function show(Faculty $faculty)
    {
        $faculty->load('majors', 'teachers');
        return view('faculties.show', compact('faculty'));
    }

    /**
     * Hiển thị form chỉnh sửa khoa
     */
    public function edit(Faculty $faculty)
    {
        return view('faculties.edit', compact('faculty'));
    }

    /**
     * Cập nhật thông tin khoa trong database
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:faculties,code,' . $faculty->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $faculty->update($request->all());

        return redirect()->route('faculties.index')
            ->with('success', 'Thông tin khoa đã được cập nhật thành công.');
    }

    /**
     * Xóa khoa khỏi database
     */
    public function destroy(Faculty $faculty)
    {
        // Kiểm tra xem khoa có ngành học không trước khi xóa
        if ($faculty->majors()->count() > 0) {
            return redirect()->route('faculties.index')
                ->with('error', 'Không thể xóa khoa này vì có ngành học liên quan.');
        }

        $faculty->delete();

        return redirect()->route('faculties.index')
            ->with('success', 'Khoa đã được xóa thành công.');
    }
} 