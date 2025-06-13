<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $semesters = Semester::with('academicYear')->paginate(10);
        return view('semesters.index', compact('semesters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicYears = AcademicYear::all();
        return view('semesters.create', compact('academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Semester::create($request->all());

        return redirect()->route('semesters.index')
            ->with('success', 'Học kỳ đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester)
    {
        $semester->load('academicYear');
        return view('semesters.show', compact('semester'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::all();
        return view('semesters.edit', compact('semester', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $semester->update($request->all());

        return redirect()->route('semesters.index')
            ->with('success', 'Học kỳ đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester)
    {
        if ($semester->grades()->count() > 0) {
            return redirect()->route('semesters.index')
                ->with('error', 'Không thể xóa học kỳ này vì có điểm liên quan.');
        }

        $semester->delete();

        return redirect()->route('semesters.index')
            ->with('success', 'Học kỳ đã được xóa thành công.');
    }
}
