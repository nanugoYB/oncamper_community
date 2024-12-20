<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\Gallery;


class GalleryController extends Controller
{
/**
 * @OA\Get(
 *     path="api/regions/galleryList",
 *     summary="특정 지역의 갤러리 리스트 조회",
 *     tags={"Gallery"},
 *     @OA\Parameter(
 *         name="region_id",
 *         in="query",
 *         required=true,
 *         description="조회할 갤러리가 속한 지역의 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공적으로 갤러리 리스트를 가져옴",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 properties={
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="region_id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Art Gallery"),
 *                     @OA\Property(property="description", type="string", example="A place for art exhibitions"),
 *                     @OA\Property(property="manager_id", type="integer", example=5),
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="잘못된 요청 파라미터",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="갤러리가 존재하지 않음",
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="message", type="string", example="갤러리가 존재하지 않습니다.")
 *             }
 *         )
 *     )
 * )
 */
    public function galleryList(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|numeric',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('region_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }
        }
        
        $region_id = $request->region_id;

        $galleries = DB::table('galleries')
        ->where('region_id', $region_id)
        ->get();

        if ($galleries->isEmpty()) {
            return response()->json([
                'message' => '갤러리가 존재하지 않습니다.'
            ], 500); 
        }

        return response()->json($galleries);
    }

/**
 * @OA\Post(
 *     path="api/regions/gallery",
 *     summary="갤러리 생성 API",
 *     tags={"Gallery"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="region_id", type="integer", example=1, description="갤러리가 속한 지역 ID"),
 *             @OA\Property(property="name", type="string", example="Art Gallery", description="갤러리 이름"),
 *             @OA\Property(property="description", type="string", example="This is an art gallery.", description="갤러리 설명"),
 *             @OA\Property(property="manager_id", type="integer", example=1, description="갤러리 관리자의 ID"),
 *             @OA\Property(property="sub_manager_1", type="integer", example=2, description="서브 매니저 1의 ID"),
 *             @OA\Property(property="sub_manager_2", type="integer", example=3, description="서브 매니저 2의 ID"),
 *             @OA\Property(property="sub_manager_3", type="integer", example=4, description="서브 매니저 3의 ID"),
 *             @OA\Property(property="sub_manager_4", type="integer", example=5, description="서브 매니저 4의 ID"),
 *             @OA\Property(property="sub_manager_5", type="integer", example=6, description="서브 매니저 5의 ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="갤러리 생성 성공",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="갤러리 생성 완료"),
 *             @OA\Property(property="gallery_info", type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="갤러리 ID"),
 *                 @OA\Property(property="region_id", type="integer", example=1, description="갤러리 지역 ID"),
 *                 @OA\Property(property="name", type="string", example="Art Gallery", description="갤러리 이름"),
 *                 @OA\Property(property="description", type="string", example="This is an art gallery.", description="갤러리 설명"),
 *                 @OA\Property(property="manager_id", type="integer", example=1, description="갤러리 관리자의 ID"),
 *                 @OA\Property(property="sub_manager_1", type="integer", example=2, description="서브 매니저 1의 ID"),
 *                 @OA\Property(property="sub_manager_2", type="integer", example=3, description="서브 매니저 2의 ID"),
 *                 @OA\Property(property="sub_manager_3", type="integer", example=4, description="서브 매니저 3의 ID"),
 *                 @OA\Property(property="sub_manager_4", type="integer", example=5, description="서브 매니저 4의 ID"),
 *                 @OA\Property(property="sub_manager_5", type="integer", example=6, description="서브 매니저 5의 ID"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-18T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-18T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     )
 * )
 */


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


        $gallery = Gallery::create([
            'region_id' => $request->region_id,
            'name' => $request->name,
            'description' => $request->description,
            'manager_id' => $request->manager_id, 
            'sub_manager_1' => $request->sub_manager_1,
            'sub_manager_2' => $request->sub_manager_2,
            'sub_manager_3' => $request->sub_manager_3,
            'sub_manager_4' => $request->sub_manager_4,
            'sub_manager_5' => $request->sub_manager_5,
        ]);

        return response()->json([
            'message' => '갤러리 생성 완료',
            'gallery_info' => $gallery
        ], 201);

    }

    /**
 * @OA\Delete(
 *     path="api/regions/gallery",
 *     summary="갤러리 삭제 API",
 *     tags={"Gallery"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="region_id", type="integer", example=1, description="갤러리가 속한 지역 ID"),
 *             @OA\Property(property="gallery_id", type="integer", example=101, description="삭제하려는 갤러리 ID"),
 *             @OA\Property(property="manager_id", type="integer", example=5, description="갤러리를 관리하는 매니저의 ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="갤러리 삭제 성공",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="갤러리가 삭제되었습니다."),
 *             @OA\Property(property="deleted_gallery", type="object",
 *                 @OA\Property(property="id", type="integer", example=101, description="삭제된 갤러리의 ID"),
 *                 @OA\Property(property="region_id", type="integer", example=1, description="갤러리의 지역 ID"),
 *                 @OA\Property(property="name", type="string", example="Art Gallery", description="갤러리 이름"),
 *                 @OA\Property(property="description", type="string", example="A beautiful gallery", description="갤러리 설명"),
 *                 @OA\Property(property="manager_id", type="integer", example=5, description="매니저의 ID")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="삭제 권한 없음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="권한이 없습니다. 갤러리를 삭제할 수 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="갤러리 찾을 수 없음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="해당 갤러리를 찾을 수 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="잘못된 요청 데이터",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="region_id", type="array", @OA\Items(type="string", example="The region_id field is required.")),
 *                 @OA\Property(property="gallery_id", type="array", @OA\Items(type="string", example="The gallery_id field is required.")),
 *                 @OA\Property(property="manager_id", type="array", @OA\Items(type="string", example="The manager_id field is required."))
 *             )
 *         )
 *     )
 * )
 */

    public function galleryDelete(Request $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|numeric',
            'gallery_id' => 'required|numeric',
            'manager_id' => 'required|numeric',
        ]);

        $region_id = $request->region_id;
        $gallery_id = $request->gallery_id;   
        $manager_id = $request->manager_id;

        $gallery = DB::table('galleries')
        ->where('region_id', $region_id)
        ->where('id', $gallery_id) 
        ->first();
        
        if (!$gallery) {
            return response()->json([
                'message' => '해당 갤러리를 찾을 수 없습니다.'
            ], 404);
        }

        if ($gallery->manager_id != $manager_id) {
            return response()->json([
                'message' => '권한이 없습니다. 갤러리를 삭제할 수 없습니다.'
            ], 403);
        }

        
        DB::table('galleries')
        ->where('region_id', $region_id)
        ->where('id', $gallery_id)
        ->delete();


        return response()->json([
            'message' => '갤러리가 삭제되었습니다.',
            'deleted_gallery' => $gallery,
        ], 200);
    }
}
