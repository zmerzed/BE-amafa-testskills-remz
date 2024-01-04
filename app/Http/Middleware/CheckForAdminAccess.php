<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckForAdminAccess
{
    public function handle(Request $request, Closure $next): mixed
    {
        // @todo Check if current user has admin access.

        return $next($request);
    }
}
