<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAuthenticatedUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->header('X-User-Id');

        if ($userId && $user = User::find((int) $userId)) {
            auth()->setUser($user);
        }

        return $next($request);
    }
}
