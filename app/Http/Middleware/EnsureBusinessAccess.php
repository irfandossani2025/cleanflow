<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureBusinessAccess {
    public function handle(Request $request, Closure $next): Response {
        $user = $request->user();
        abort_unless($user && ($user->business_id || $user->is_platform_admin), 403, 'A business workspace is required.');
        return $next($request);
    }
}
