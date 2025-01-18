<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustBeLoggedIn {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        /** @var StatefulGuard $auth */
        $auth = auth();
        if ($auth->check()) {
            return $next($request);
        }
        return redirect('/')->with('failure', 'You must be logged in.');
    }
}
