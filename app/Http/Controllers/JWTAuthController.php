<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class JWTAuthController extends Controller
{
    // User registration
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_name'=> 'required|string|max:255', 
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422); 
        }

        
        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);

    

        return response()->json([
            'message' => '유저생성 완료',
            'user' => $user
        ], 201); // 성공시 201 상태 코드
    }


    // User login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = DB::table('users')
        ->where('email', $request->email)
        ->first();

        if (!$user) {
            return response()->json(['message' => '계정이 존재하지 않습니다.'], 401);
        }
        
        if (Hash::check($request->password, $user->password)) {
            
        } else {
            
            return response()->json(['message' => '비밀번호가 동일하지 않습니다.'], 401);
        }


        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => '비밀번호 값이 유효하지 않습니다.'], 401);
            }

            // Get the authenticated user.
    

            // (optional) Attach the role to the token.
            $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);

            return response()->json(compact('token'));
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    // Get authenticated user
    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        return response()->json(compact('user'));
    }

    // User logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => '성공적으로 로그아웃 되었습니다.']);
    }
}