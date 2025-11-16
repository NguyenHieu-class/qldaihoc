<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Hiển thị form đổi mật khẩu cho giáo viên và sinh viên.
     */
    public function edit(): View
    {
        return view('auth.passwords.change');
    }

    /**
     * Cập nhật mật khẩu sau khi xác minh mật khẩu cũ.
     */
    public function update(): RedirectResponse
    {
        $data = request()->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('Mật khẩu hiện tại không chính xác.'),
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('password.change')->with('success', __('Mật khẩu đã được thay đổi thành công.'));
    }
}
