<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $token = $request->cookie('gmwr_token');

            if ( !$token ) {
                return response()->json(['error'=>'Token not found'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            $request->merge(['auth_user'=>$user]);
        } catch ( JWTException $e ) {
            return response()->json(['error'=>'Token not valid'], 401);
        }

        return $next($request);
    }
}
