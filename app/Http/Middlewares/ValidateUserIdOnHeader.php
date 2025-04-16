<?php

namespace App\Http\Middlewares;

use App\Exceptions\Business\UserIdNotProvidedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateUserIdOnHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$request->header('user-id')) {
                throw new UserIdNotProvidedException();
            }

            return $next($request);
        } catch (UserIdNotProvidedException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
