<?php

namespace App\Http\Controllers;

use App\Models\ClassSizeCoefficient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSizeCoefficientController extends Controller
{
    public function index()
    {
        $coefficients = ClassSizeCoefficient::paginate(10);
        return view('class_size_coefficients.index', compact('coefficients'));
    }

    public function create()
    {
        return view('class_size_coefficients.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'min_students' => 'required|integer',
            'max_students' => 'required|integer|gte:min_students',
            'coefficient' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ClassSizeCoefficient::create($request->all());

        return redirect()->route('class-size-coefficients.index')
            ->with('success', 'Hệ số sĩ số đã được tạo.');
    }

    public function edit(ClassSizeCoefficient $classSizeCoefficient)
    {
        return view('class_size_coefficients.edit', compact('classSizeCoefficient'));
    }

    public function update(Request $request, ClassSizeCoefficient $classSizeCoefficient)
    {
        $validator = Validator::make($request->all(), [
            'min_students' => 'required|integer',
            'max_students' => 'required|integer|gte:min_students',
            'coefficient' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $classSizeCoefficient->update($request->all());

        return redirect()->route('class-size-coefficients.index')
            ->with('success', 'Hệ số sĩ số đã được cập nhật.');
    }

    public function destroy(ClassSizeCoefficient $classSizeCoefficient)
    {
        $classSizeCoefficient->delete();

        return redirect()->route('class-size-coefficients.index')
            ->with('success', 'Hệ số sĩ số đã được xóa.');
    }
}
