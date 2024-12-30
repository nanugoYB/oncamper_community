<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class ImageController extends Controller
{   
    /**
 * @OA\Post(
 *     path="/api/upload-image",
 *     summary="이미지 업로드",
 *     description="이미지 파일을 업로드하고 해당 파일의 URL을 반환합니다.",
 *     tags={"Image"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"image"},
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="업로드할 이미지 파일"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="업로드 성공",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="url",
 *                 type="string",
 *                 description="업로드된 이미지의 URL"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효하지 않은 요청",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="에러 메시지"
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 description="상세한 검증 에러 정보"
 *             )
 *         )
 *     )
 * )
 */

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('uploads', 'public');

        return response()->json(['url' => asset("storage/$path")]);
        
    }
}
