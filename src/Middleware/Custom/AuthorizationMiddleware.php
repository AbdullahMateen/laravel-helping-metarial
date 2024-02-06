<?php

namespace AbdullahMateen\LaravelHelpingMaterial\Middleware\Custom;

use Closure;
use Illuminate\Http\Request;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$userLevel)
    {
        abort_unless(is_level($userLevel), 404);
        return $next($request);
    }
}
