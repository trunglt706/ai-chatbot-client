<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Nếu người dùng bị khóa và không phải đang ở trang thông báo bị khóa
            if ($user->status === 'blocked' && !$request->routeIs('account.blocked')) {
                // Đăng xuất người dùng (tùy chọn, nhưng nên làm để đảm bảo)
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Chuyển hướng về trang bị khóa tài khoản
                return redirect()->route('account.blocked')->with('status', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
            }
        }

        return $next($request);
    }
}
