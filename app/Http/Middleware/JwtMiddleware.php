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
            'api/v1/idCheck',
            'api/menus',
            'api/menu',
        ];
    
        foreach ( $except as $ex ) {
            $exp = "/".preg_quote($ex, "/")."/i";
            $path = $request->path();
            if ( preg_match_all($exp, $path) ) {
                return $next($request);
            }
        }

        try {
            $token = $request->cookie('gmwr_token');

            if (!$token) {
                return response()->json(['success'=>false, 'code'=> 'T-002', 'message' => 'access token expired'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            $request->merge(['auth_user'=>$user]);
        } catch ( TokenExpiredException $e ) {
            // access token 만료 → refresh 시도
            $refreshToken = $request->cookie('gmwr_refreshToken');

            if (!$refreshToken) {
                return response()->json(['success'=> false, 'code'=>'T-004', 'message' => 'not refresh token'], 401);
            }

            return response()->json(['success'=>false, 'code'=> 'T-002', 'message' => 'access token expired'], 401);

        } catch ( JWTException $e ) {
            return response()->json(['success'=> false, 'code'=>'T-001', 'message'=>'Token not valid'], 401);
        }

        return $next($request);
    }
}
