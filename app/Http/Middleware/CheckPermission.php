<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view')
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->canDo($module, $action)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Không có quyền thực hiện thao tác này.'], 403);
            }
            return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
