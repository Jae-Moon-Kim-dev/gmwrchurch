<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {

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
            $createToken = auth()->user()->createToken('gmwoori')->plainTextToken;
            $splitToken = explode("|", $createToken);
            $token = $splitToken[1];
            return response()->json(['success'=>true, 'data'=>$token], 200);
            
        } else {
            return response()->json(['success'=>false, 'message'=>'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $result = $request->user()->currentAccessToken()->delete();

        if($result) {
            $response = response()->json(['error'=>false,'message'=>'User logout successfully.'], 200);
        } else {
            $response = response()->json(['error'=>true,'message'=>'Something is wrong.'], 401);
        }

        return $response;
    }


}
