<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\Admin\AdminMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Auth\AuthService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller {
    protected $authService;
    protected $adminMenuService;
    protected $logger;

    public function __construct(AuthService $authService, AdminMenuService $adminMenuService) {
        $this->authService = $authService;
        $this->adminMenuService = $adminMenuService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function register(LoginRequest $request) {
        // $this->validate($request, [
        //     'name' => 'required|min:4',
        //     'email' => 'required|email',
        //     'password' => 'required|min:8',
        // ]);

        $user = User::create([
            'name'=> $request->name,
            'mem_id'=> $request->mem_id,
            'gender1'=> $request->gender1,
            'email'=> $request->email,
            'cel_num'=> $request->cel_num,
            'birth_date'=> $request->birth_date,
            'parent_nm'=> $request->parent_nm,
            'gender2'=> $request->gender2,
            'parent_birth_date'=> $request->parent_birth_date,
            'parent_cel_num'=> $request->parent_cel_num,
            'mem_agr1'=> $request->mem_agr1,
            'mem_agr2'=> $request->mem_agr2,
            'password'=> bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['success'=> true, 'message'=> '사용자 등록 완료'], 201);
    }

    public function login(Request $request) {
        $secure = app()->environment('production');

        try {
            //유효성 검사
            $validator = Validator::make($request->all(), [
                'mem_id' => 'required|string',
                'password' => 'required|string|min:8'
            ]);

            if($validator->fails())
            {
                return response()->json(['success'=> false, 'message' => '비밀번호가 일치하지 않습니다. 비밀번호를 확인해 주세요.'], 200);
            }

            $data = [
                'mem_id'=>$request->mem_id,
                'password'=>$request->password,
            ];

            if(!$token = JWTAuth::attempt($data))
            {
                return response()->json(['success'=>false, 'code'=> 'T-005', 'message' => '비밀번호가 일치하지 않습니다. 비밀번호를 확인해 주세요.'], 401);
            }

            $refreshToken = JWTAuth::claims(['type'=>'refresh'])->fromUser(Auth::user());

            // return response()->json(['success'=>true])->cookie('gmwr_token', $token, 15, '/', null, $secure, true)
            //                                                  ->cookie('gmwr_refreshToken', $refreshToken, 43200, '/', null, $secure, true);
            return response()->json(['success'=>true])->cookie('gmwr_token', $token, 1, '/', null, $secure, true)
                                                             ->cookie('gmwr_refreshToken', $refreshToken, 43200, '/', null, $secure, true);
        } catch ( JWTException $e ) {
            return response()->json(['success'=>false, 'message'=>'Could not create token'], 500);
        }
    }

    public function refresh(Request $request) {
        $secure = app()->environment('production');

        $refreshToken = $request->cookie('gmwr_refreshToken');

        if ( !$refreshToken ) {
            return response()->json(['success'=> false, 'code'=> 'T-004', 'message'=>'No refresh token'], 401);
        }

        try {
            JWTAuth::setToken($refreshToken);
            $user = JWTAuth::authenticate();

            $payload = JWTAuth::getPayload();
            if ( $payload['type'] !== 'refresh' ) {
                return response()->json(['success'=> false, 'code'=>'T-001', 'message'=>'Invalid refresh token type'], 403);
            }

            $newAccessToken = JWTAuth::fromUser($user);

            // return response()->json(['success'=>true, 'message'=>'Token refreshed'], 200)->cookie('gmwr_token', $newAccessToken, 15, '/', null, $secure, true);
            return response()->json(['success'=>true, 'message'=>'Token refreshed'], 200)->cookie('gmwr_token', $newAccessToken, 1, '/', null, $secure, true);
        } catch (TokenExpiredException $e) {
            return response()->json(['success'=>false, 'code'=>'T-003', 'message'=>'Refresh token expired'], 401);
        } catch ( JWTException $e ) {
            return response()->json(['success'=>false, 'code'=>'T-001', 'message'=>'Token invalid'], 401);
        }
    }

    public function getUser(Request $request) {
        try {
            $user = $request->get("auth_user");

            if ( !$user ) {
                return response()->json(['success'=>false, 'code'=>'T-005', 'message'=>'User not found'], 404);
            }

        } catch (JWTException $e) {
            return response()->json(['success'=>false, 'code'=>'T-001', 'message'=>'Invalid token'], 400);
        }

        return response()->json(['success'=>true,'data'=>$user]);
    }

    public function idCheck(Request $request) {
        $user = User::where('mem_id', $request->mem_id)->first();

        if ( !$user ) {
            return response()->json(['success'=>true, 'data'=>true, 'message'=>'User not found'], 200);
        } else {
            return response()->json(['success'=>true, 'data'=>false, 'message'=>'User exists'], 200);
        }
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['success'=>true,'message'=>'정상적으로 로그아웃 되었습니다.'], 200)->cookie('gmwr_token', '', -1)
                                                                                                 ->cookie('gmwr_refreshToken', '', -1);
    }

    public function getMenus() {
        $this->logger->info('===getMenus===');

        $menus = $this->authService->getMenus();

        $menuList = $this->authService->getRecursiveMenu($menus);

        if ( !$menuList ) {
            return response()->json(['success'=>true, 'data'=>null, 'message'=>'No Data'], 200);
        } else {
            return response()->json(['success'=>true, 'data'=>$menuList, 'message'=>''], 200);
        }
    }

    public function getMenu(string $id) {
        $this->logger->info('===getMenu===');

        $menu = $this->authService->getMenu($id);

        $menuList = $this->authService->getRecursiveSideMenu((object)$menu);

        if ( !$menuList ) {
            return response()->json(['success'=>true, 'data'=>null, 'message'=>'No Data'], 200);
        } else {
            return response()->json(['success'=>true, 'data'=>$menuList, 'message'=>''], 200);
        }
    }

}
