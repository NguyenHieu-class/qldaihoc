<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DegreeController extends Controller
{
    /**
     * Hiển thị danh sách học vị
     */
    public function index()
    {
        $degrees = Degree::paginate(10);
        return view('degrees.index', compact('degrees'));
    }

    /**
     * Hiển thị form tạo học vị mới
     */
    public function create()
    {
        return view('degrees.create');
    }

    /**
     * Lưu học vị mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'coefficient' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Degree::create($request->all());

        return redirect()->route('degrees.index')
            ->with('success', 'Học vị đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của học vị
     */
    public function show(Degree $degree)
    {
        $degree->load('teachers');
        return view('degrees.show', compact('degree'));
    }

    /**
     * Hiển thị form chỉnh sửa học vị
     */
    public function edit(Degree $degree)
    {
        return view('degrees.edit', compact('degree'));
    }

    /**
     * Cập nhật thông tin học vị trong database
     */
    public function update(Request $request, Degree $degree)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'coefficient' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $degree->update($request->all());

        return redirect()->route('degrees.index')
            ->with('success', 'Thông tin học vị đã được cập nhật thành công.');
    }

    /**
     * Xóa học vị khỏi database
     */
    public function destroy(Degree $degree)
    {
        if ($degree->teachers()->count() > 0) {
            return redirect()->route('degrees.index')
                ->with('error', 'Không thể xóa học vị này vì có giáo viên liên quan.');
        }

        $degree->delete();

        return redirect()->route('degrees.index')
            ->with('success', 'Học vị đã được xóa thành công.');
    }
}
