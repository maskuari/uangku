<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanUseFinances
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
                return redirect()->route('admin.index');
            }

            abort(403);
        }

        return $next($request);
    }
}
