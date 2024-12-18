<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\Gallery;


class GalleryPostController extends Controller
{

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
