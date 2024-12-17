<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
class GalleryController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/regions/gallery",
 *     summary="갤러리 목록 조회",
 *     tags={"Gallery"},
 *     @OA\Response(
 *         response=200,
 *         description="갤러리 목록이 성공적으로 반환됨",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Summer Collection"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-17T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-17T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="갤러리가 존재하지 않음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="갤러리가 존재하지 않습니다.")
 *         )
 *     )
 * )
 */
    public function galleryList()
    {
        $galleries = DB::table('galleries')->get();

        if ($galleries->isEmpty()) {
            return response()->json([
                'message' => '갤러리가 존재하지 않습니다.'
            ], 500); 
        }

        return response()->json($galleries);
    }


    public function galleryAdd(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'manager_id' => 'required|numeric',
            'sub_manager_1' => 'numeric',
            'sub_manager_2' => 'numeric',
            'sub_manager_3' => 'numeric',
            'sub_manager_4' => 'numeric',
            'sub_manager_5' => 'numeric',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('region_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('name')){
                return response()->json([
                    'error' => '갤러리 이름은 필수 입력값입니다.'
                ],422);
            }

            if($errors->has('description')){
                return response()->json([
                    'error' => '갤러리에 대한 간략한 설명을 적어주세요.'
                ],422);
            }

            if($errors->has('manager_id')){
                return response()->json([
                    'error' => '로그인 후 이용해주세요.'
                ],422);
                //이후 리다이렉트 하도록 하는것도 괜찮을듯?
            }
        }

        


    }

    public function galleryDelete(): JsonResponse 
    {
        
    }
}
