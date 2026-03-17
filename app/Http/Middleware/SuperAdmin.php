<?php
namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class SuperAdmin
{
    public function handle($request, Closure $next)
    {
        if ( Auth::user()->type !== 1 ) {
            return redirect('/')->with('error',__('You are not Super Admin'));
        }
        return $next($request);
    }
}
