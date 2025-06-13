<?php

namespace App\Http\Controllers;

use App\Models\DegreeCoefficient;
use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DegreeCoefficientController extends Controller
{
    public function index()
    {
        $coefficients = DegreeCoefficient::with('degree')->paginate(10);
        return view('degree_coefficients.index', compact('coefficients'));
    }

    public function create()
    {
        $degrees = Degree::all();
        return view('degree_coefficients.create', compact('degrees'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'degree_id' => 'required|exists:degrees,id',
            'coefficient' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DegreeCoefficient::updateOrCreate(
            ['degree_id' => $request->degree_id],
            ['coefficient' => $request->coefficient]
        );

        return redirect()->route('degree-coefficients.index')
            ->with('success', 'Hệ số học vị đã được lưu.');
    }

    public function edit(DegreeCoefficient $degreeCoefficient)
    {
        $degrees = Degree::all();
        return view('degree_coefficients.edit', compact('degreeCoefficient', 'degrees'));
    }

    public function update(Request $request, DegreeCoefficient $degreeCoefficient)
    {
        $validator = Validator::make($request->all(), [
            'degree_id' => 'required|exists:degrees,id',
            'coefficient' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $degreeCoefficient->update($request->all());

        return redirect()->route('degree-coefficients.index')
            ->with('success', 'Hệ số học vị đã được cập nhật.');
    }

    public function destroy(DegreeCoefficient $degreeCoefficient)
    {
        $degreeCoefficient->delete();

        return redirect()->route('degree-coefficients.index')
            ->with('success', 'Hệ số học vị đã được xóa.');
    }
}
