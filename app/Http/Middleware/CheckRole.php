<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Xử lý một request đến.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        foreach ($roles as $role) {
            // Kiểm tra nếu role của user khớp với role được yêu cầu
            if ($user->role == $role) {
                return $next($request);
            }
        }
        
        // Nếu không có quyền truy cập
        return redirect('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
    }
} 