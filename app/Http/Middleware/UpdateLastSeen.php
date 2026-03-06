<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->update([
                'is_online' => true,
                'last_seen' => now()
            ]);
        }

        return $next($request);
    }
}
