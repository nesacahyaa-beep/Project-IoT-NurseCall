<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
        $key = (string) config('nursecall.device_key');

        if ($key === '' || ! hash_equals($key, (string) $request->header('X-Device-Key'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
