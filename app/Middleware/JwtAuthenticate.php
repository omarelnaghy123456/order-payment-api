<?php

namespace App\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtAuthenticate
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return $this->errorResponse('User not found', 404);
            }
        } catch (TokenInvalidException $e) {
            return $this->errorResponse('Invalid token', 401);
        } catch (TokenExpiredException $e) {
            return $this->errorResponse('Token has expired', 401);
        } catch (JWTException $e) {
            return $this->errorResponse('Token not provided', 401);
        }
        return $next($request);
    }
}
