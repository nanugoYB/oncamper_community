<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="api/register",
 *     summary="유저등록을 위한 API",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user_name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="유저가 성공적으로 등록됨",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="유저생성 완료"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-17T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-17T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="일부 필드에서 정합성 검사에서 오류 발생",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="user_name", type="array", @OA\Items(type="string", example="The user_name field is required.")),
 *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
 *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field is required.")),
 *                 @OA\Property(property="password_confirmation", type="array", @OA\Items(type="string", example="The password confirmation does not match."))
 *             )
 *         )
 *     )
 * )
 */
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

        // 데이터베이스에 새 유저 저장
        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        // 성공적인 응답 반환
        return response()->json([
            'message' => '유저생성 완료',
            'user' => $user
        ], 201); // 성공시 201 상태 코드
    }

    
}
