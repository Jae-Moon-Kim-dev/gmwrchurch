<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $except = [
            'api/v1/login',
            'api/v1/register',
            'api/v1/refreshToken',
        ];
    
        if (in_array($request->path(), $except)) {
            return $next($request);
        }

        try {
            $token = $request->cookie('gmwr_token');

            if (!$token) {
                return response()->json(['success'=>false, 'code'=> 'T-002', 'message' => 'access token expired'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            $request->merge(['auth_user'=>$user]);
        } catch ( JWTException $e ) {
            return response()->json(['success'=> false, 'code'=>'T-001', 'message'=>'Token not valid'], 401);
        }

        return $next($request);
    }
}
