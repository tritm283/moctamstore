<?php
namespace App\Http\Middleware;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user=$request->user();
        if (!$user || $user->role !== UserRole::ADMIN || !$user->is_active || !$user->currentAccessToken()?->can('admin')) {
            return response()->json(['message'=>'Bạn không có quyền quản trị.'],403);
        }
        return $next($request);
    }
}
