<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Auth\AuthService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AuthController extends Controller {
    protected $authService;
    protected $logger;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log'), Logger::INFO));
    }

    public function register(LoginRequest $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|min:4',
        //     'email' => 'required|email',
        //     'password' => 'required|min:8',
        // ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password),
        ]);

        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['token'=>$token], 200);
    }

    public function login(Request $request)
    {
        //유효성 검사
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = [
            'email'=>$request->email,
            'password'=>$request->password,
        ];

        if (auth()->attempt($data))
        {
            // $token = auth()->user()->createToken('gmwoori')->plainTextToken;

            // $user = $this->authService->getUserById($request->email);

            // return response()->json(['success'=>true, 'data'=>$user], 200)->cookie('gmwoori', $token, 60*24, '/', null, true, true);
            $request->session()->regenerate();
            return response()->json(['success'=>true, 'data'=>auth()->user()]);
            
        } else {
            return response()->json(['success'=>false, 'message'=>'비밀번호가 일치하지 않습니다. 비밀번호를 확인해 주세요.'], 200);
        }
    }

    public function logout(Request $request)
    {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success'=>true,'message'=>'정상적으로 로그아웃 되었습니다.'], 200);;
    }


}
