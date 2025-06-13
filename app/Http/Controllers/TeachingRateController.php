<?php

namespace App\Http\Controllers;

use App\Models\TeachingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeachingRateController extends Controller
{
    public function index()
    {
        $rates = TeachingRate::paginate(10);
        return view('teaching_rates.index', compact('rates'));
    }

    public function create()
    {
        return view('teaching_rates.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        TeachingRate::create($request->all());

        return redirect()->route('teaching-rates.index')
            ->with('success', 'Mức lương đã được tạo thành công.');
    }

    public function edit(TeachingRate $teachingRate)
    {
        return view('teaching_rates.edit', compact('teachingRate'));
    }

    public function update(Request $request, TeachingRate $teachingRate)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $teachingRate->update($request->all());

        return redirect()->route('teaching-rates.index')
            ->with('success', 'Mức lương đã được cập nhật thành công.');
    }

    public function destroy(TeachingRate $teachingRate)
    {
        $teachingRate->delete();

        return redirect()->route('teaching-rates.index')
            ->with('success', 'Mức lương đã được xóa thành công.');
    }
}
