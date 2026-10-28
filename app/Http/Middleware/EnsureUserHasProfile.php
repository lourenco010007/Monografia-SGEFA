<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasProfile
{
    public function handle(Request $request, Closure $next, string ...$profiles): Response
    {
        if (! $request->user() || ! in_array($request->user()->perfil, $profiles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
