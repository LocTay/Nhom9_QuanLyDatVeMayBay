<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ) {
        if (!Auth::check()) {
            abort(401, 'Bạn cần đăng nhập để truy cập.');
        }

        if (!in_array(Auth::user()->role, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
