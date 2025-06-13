<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Hiển thị danh sách môn học
     */
    public function index(Request $request)
    {
        $query = Subject::query();
        
        // Tìm kiếm theo tên hoặc mã môn học
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo số tín chỉ
        if ($request->has('credits') && $request->credits) {
            $query->where('credits', $request->credits);
        }
        
        $subjects = $query->paginate(10);
        $creditOptions = Subject::distinct()->orderBy('credits')->pluck('credits');
        
        return view('subjects.index', compact('subjects', 'creditOptions'));
    }

    /**
     * Hiển thị form tạo môn học mới
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Lưu môn học mới vào database
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects',
            'credits' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Subject::create($request->all());

        return redirect()->route('subjects.index')
            ->with('success', 'Môn học đã được tạo thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết của môn học
     */
    public function show(Subject $subject)
    {
        $subject->load('grades.student');
        return view('subjects.show', compact('subject'));
    }

    /**
     * Hiển thị form chỉnh sửa môn học
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Cập nhật thông tin môn học trong database
     */
    public function update(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'credits' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $subject->update($request->all());

        return redirect()->route('subjects.index')
            ->with('success', 'Thông tin môn học đã được cập nhật thành công.');
    }

    /**
     * Xóa môn học khỏi database
     */
    public function destroy(Subject $subject)
    {
        // Kiểm tra xem môn học có điểm số không trước khi xóa
        if ($subject->grades()->count() > 0) {
            return redirect()->route('subjects.index')
                ->with('error', 'Không thể xóa môn học này vì có điểm số liên quan.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Môn học đã được xóa thành công.');
    }
} 