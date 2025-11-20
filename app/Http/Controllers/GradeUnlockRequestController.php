<?php

namespace App\Http\Controllers;

use App\Models\GradeUnlockRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeUnlockRequestController extends Controller
{
    public function index(): View
    {
        $requests = GradeUnlockRequest::with([
            'classSection.subject',
            'classSection.courseOffering.semester.academicYear',
            'teacher',
            'processedBy',
        ])->latest()->get();

        return view('admin.grade_unlock_requests.index', compact('requests'));
    }

    public function approve(GradeUnlockRequest $gradeUnlockRequest): RedirectResponse
    {
        if ($gradeUnlockRequest->status !== GradeUnlockRequest::STATUS_PENDING) {
            return back()->with('error', __('Yêu cầu đã được xử lý trước đó.'));
        }

        if ($gradeUnlockRequest->classSection) {
            $gradeUnlockRequest->classSection->update([
                'grades_locked' => false,
                'grades_locked_at' => null,
            ]);
        }

        $gradeUnlockRequest->update([
            'status' => GradeUnlockRequest::STATUS_APPROVED,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', __('Đã mở khoá quyền chỉnh sửa điểm cho lớp :code.', [
            'code' => $gradeUnlockRequest->classSection->code ?? '',
        ]));
    }

    public function reject(GradeUnlockRequest $gradeUnlockRequest): RedirectResponse
    {
        if ($gradeUnlockRequest->status !== GradeUnlockRequest::STATUS_PENDING) {
            return back()->with('error', __('Yêu cầu đã được xử lý trước đó.'));
        }

        $gradeUnlockRequest->update([
            'status' => GradeUnlockRequest::STATUS_REJECTED,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', __('Đã từ chối yêu cầu mở khoá điểm của giáo viên.'));
    }
}
