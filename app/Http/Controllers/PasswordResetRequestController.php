<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetRequestController extends Controller
{
    public function create(): View
    {
        return view('auth.passwords.request');
    }

    public function store(): RedirectResponse
    {
        $data = request()->validate([
            'identifier' => ['required', 'string', 'max:255'],
        ]);

        $identifier = $data['identifier'];

        $student = Student::with('user')
            ->where('student_id', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        $teacher = Teacher::with('user')
            ->where('teacher_id', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        $requester = null;

        if ($student && $student->user && $student->user->isStudent()) {
            $requester = $student;
        } elseif ($teacher && $teacher->user && $teacher->user->isTeacher()) {
            $requester = $teacher;
        }

        if (!$requester || !$requester->user) {
            return back()->withErrors([
                'identifier' => __('Không tìm thấy tài khoản sinh viên hoặc giáo viên khớp với thông tin bạn cung cấp.'),
            ])->withInput();
        }

        $resetRequest = PasswordResetRequest::firstOrCreate(
            [
                'user_id' => $requester->user->id,
                'status' => 'pending',
            ],
            [
                'requester_id' => $requester->id,
                'requester_type' => get_class($requester),
            ]
        );

        return back()->with('status', __('Yêu cầu đã được gửi tới quản trị viên. Vui lòng chờ xác nhận.'))
            ->with('matched_user', [
                'name' => $requester->full_name,
                'role' => $requester instanceof Student ? __('Sinh viên') : __('Giáo viên'),
                'identifier' => $requester instanceof Student ? $requester->student_id : $requester->teacher_id,
                'email' => $requester->email,
                'status' => $resetRequest->status,
            ]);
    }

    public function index(): View
    {
        $requests = PasswordResetRequest::with(['user', 'requester'])->latest()->get();

        return view('admin.password_reset_requests.index', compact('requests'));
    }

    public function process(): RedirectResponse
    {
        $data = request()->validate([
            'request_ids' => ['required', 'array'],
            'request_ids.*' => ['integer', 'exists:password_reset_requests,id'],
        ]);

        $requests = PasswordResetRequest::with(['user', 'requester'])
            ->whereIn('id', $data['request_ids'])
            ->get();

        $processedCount = 0;

        foreach ($requests as $resetRequest) {
            if ($resetRequest->status !== 'pending' || !$resetRequest->user || !$resetRequest->requester) {
                continue;
            }

            $requester = $resetRequest->requester;
            $defaultPassword = $requester instanceof Student
                ? $requester->student_id
                : ($requester instanceof Teacher ? $requester->teacher_id : null);

            if (!$defaultPassword) {
                continue;
            }

            $resetRequest->user->update([
                'password' => Hash::make($defaultPassword),
            ]);

            $resetRequest->update([
                'status' => 'processed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $processedCount++;
        }

        return back()->with('success', __('Đã đặt lại mật khẩu cho :count yêu cầu.', ['count' => $processedCount]));
    }
}
