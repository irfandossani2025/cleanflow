<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isDemo() || $request->isMethodSafe() || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'The live demo is read-only. Create your workspace to add or change data.');
    }
}
