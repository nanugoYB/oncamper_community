<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\GalleryPost;
use Mews\Purifier\Purifier;

class GalleryPostController extends Controller
{
    protected $purifier;

    public function __construct(Purifier $purifier)
{
    $this->purifier = $purifier;
}
    /**
 * @OA\Get(
 *     path="/api/regions/gallery/postList",
 *     summary="Get paginated list of gallery posts",
 *     description="Retrieve a paginated list of posts for a specific gallery based on gallery_id.",
 *     operationId="getGalleryPosts",
 *     tags={"Gallery Posts"},
 *     @OA\Parameter(
 *         name="gallery_id",
 *         in="query",
 *         description="ID of the gallery for which posts are to be retrieved",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response containing paginated gallery posts",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="current_page", type="integer", description="Current page number"),
 *             @OA\Property(property="data", type="array", description="List of gallery posts", @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", description="Post ID"),
 *                 @OA\Property(property="gallery_id", type="integer", description="Gallery ID"),
 *                 @OA\Property(property="user_id", type="integer", description="User ID of the post author"),
 *                 @OA\Property(property="user_name", type="string", description="Username of the post author"),
 *                 @OA\Property(property="title", type="string", description="Title of the post"),
 *                 @OA\Property(property="content", type="string", description="Content of the post"),
 *                 @OA\Property(property="views", type="integer", description="View count of the post"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", description="Post creation timestamp"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Post update timestamp")
 *             )),
 *             @OA\Property(property="first_page_url", type="string", description="URL of the first page"),
 *             @OA\Property(property="last_page", type="integer", description="Last page number"),
 *             @OA\Property(property="last_page_url", type="string", description="URL of the last page"),
 *             @OA\Property(property="next_page_url", type="string", nullable=true, description="URL of the next page"),
 *             @OA\Property(property="prev_page_url", type="string", nullable=true, description="URL of the previous page"),
 *             @OA\Property(property="total", type="integer", description="Total number of posts"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="No posts found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="게시글이 존재하지 않습니다")
 *         )
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

    public function postList(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'gallery_id' => 'required|numeric'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('gallery_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }
        }
        
        $perPage = 10;

        $gallery_posts = DB::table('gallery_posts')
        ->where('gallery_id', $request->gallery_id)
        ->orderBy('created_at', 'desc') // 최신 게시글부터 정렬
        ->paginate($perPage); // 페이지네이션 적용


        if ($gallery_posts->isEmpty()) {
            return response()->json([
                'message' => '게시글이 존재하지 않습니다'
            ], 500); 
        }

        return response()->json($gallery_posts);
    }

/**
 * @OA\Get(
 *     path="/api/regions/gallery/post",
 *     summary="게시글 조회",
 *     description="게시글을 조회하고 조회수를 증가시킵니다.",
 *     tags={"Gallery Posts"},
 *     @OA\Parameter(
 *         name="gallery_id",
 *         in="query",
 *         description="갤러리 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="post_id",
 *         in="query",
 *         description="게시글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="게시글 조회 성공 및 조회수 증가",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="게시글 ID"
 *             ),
 *             @OA\Property(
 *                 property="gallery_id",
 *                 type="integer",
 *                 description="갤러리 ID"
 *             ),
 *             @OA\Property(
 *                 property="user_name",
 *                 type="string",
 *                 description="글쓴 유저 이름"
 *             ),
 *             @OA\Property(
 *                 property="content",
 *                 type="string",
 *                 description="게시글 내용"
 *             ),
 *             @OA\Property(
 *                 property="views",
 *                 type="integer",
 *                 description="조회수"
 *             ),
 *             @OA\Property(
 *                 property="created_at",
 *                 type="string",
 *                 format="date-time",
 *                 description="게시글 생성 시간"
 *             ),
 *             @OA\Property(
 *                 property="updated_at",
 *                 type="string",
 *                 format="date-time",
 *                 description="게시글 수정 시간"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="부적절한 요청 (gallery_id 또는 post_id 누락)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="부적절한 접근입니다."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="게시글을 찾을 수 없음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="게시글이 존재하지 않습니다"
 *             )
 *         )
 *     )
 * )
 */


    public function viewPost(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'gallery_id' => 'required|numeric',
            'post_id' => 'required|numeric',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('gallery_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('post_id')){
                return response()->json([
                    'error' => '게시글이 존재하지 않습니다.'
                ],422);
            }
        }
        

        $gallery_post = DB::table('gallery_posts')
        ->where('gallery_id', $request->gallery_id)
        ->where('id', $request->post_id)
        ->get();

        if ($gallery_post->isEmpty()) {
            return response()->json([
                'message' => '게시글이 존재하지 않습니다'
            ], 500); 
        }

        DB::table('gallery_posts')
        ->where('gallery_id', $request->gallery_id)
        ->where('id', $request->post_id)
        ->increment('views'); // views 컬럼 값 1 증가


        return response()->json($gallery_post);
    }

/**
 * @OA\Post(
 *     path="/api/regions/gallery/post",
 *     summary="게시글 추가",
 *     description="새로운 게시글을 추가합니다.",
 *     tags={"Gallery Posts"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"gallery_id", "user_id", "user_name", "title", "content"},
 *             @OA\Property(
 *                 property="gallery_id",
 *                 type="integer",
 *                 description="갤러리 ID"
 *             ),
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 description="유저 ID"
 *             ),
 *             @OA\Property(
 *                 property="user_name",
 *                 type="string",
 *                 description="유저 이름"
 *             ),
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="게시글 제목"
 *             ),
 *             @OA\Property(
 *                 property="content",
 *                 type="string",
 *                 description="게시글 내용"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="게시글 생성 성공",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="게시글 생성 완료"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효하지 않은 입력 데이터",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="부적절한 접근입니다."
 *             )
 *         )
 *     )
 * )
 */

    public function postAdd(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'gallery_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'user_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('gallery_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('user_id')){
                return response()->json([
                    'error' => '로그인 후 이용해주세요.'
                ],422);
            }

            if($errors->has('user_name')){
                return response()->json([
                    'error' => '유저 네임이 없습니다.'
                ],422);
            }

            if($errors->has('title')){
                return response()->json([
                    'error' => '제목을 입력해주세요'
                ],422);
            }

            if($errors->has('content')){
                return response()->json([
                    'error' => '내용을 입력해주세요'
                ],422);
            }
        }

        //purifier를 통해서 content 내부의 위험요소 제거 (script, img의 src중 서버에 없는 src등)
        $content = $this->purifier->clean($request->input('content'));

        $galleryPost = GalleryPost::create([
            'gallery_id' => $request->gallery_id,
            'user_id' => $request->user_id,
            'user_name' => $request->user_name,
            'title' => $request->title, 
            'content' => $content
        ]); 

        return response()->json([
            'message' => '게시글 생성 완료' 
        ], 201);

    }

 
/**
 * @OA\Delete(
 *     path="/api/regions/gallery/post",
 *     summary="게시글 삭제",
 *     description="사용자가 작성한 게시글을 삭제합니다.",
 *     tags={"Gallery Posts"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"gallery_id", "user_id"},
 *             @OA\Property(
 *                 property="gallery_id",
 *                 type="integer",
 *                 description="갤러리 ID"
 *             ),
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 description="사용자 ID (게시글 작성자 ID)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="게시글 삭제 성공",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="갤러리가 삭제되었습니다."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효하지 않은 입력 데이터",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="부적절한 접근입니다."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="삭제 권한이 없음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="글을 삭제할 권한이 없습니다."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="게시글을 찾을 수 없음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="해당 게시글을 찾을 수 없습니다."
 *             )
 *         )
 *     )
 * )
 */

    public function postDelete(Request $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'gallery_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('gallery_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('user_id')){
                return response()->json([
                    'error' => '글 작성자만 지울 수 있습니다.'
                ],422);
            }
        }

        $gallery_id = $request->gallery_id;   
        $user_id = $request->user_id;

        $post = DB::table('gallery_posts')
        ->where('gallery_id', $gallery_id)
        ->where('user_id', $user_id) 
        ->first();

        if (!$post) {
            return response()->json([
                'message' => '해당 게시글을 찾을 수 없습니다.'
            ], 404);
        }

        if ($post->user_id != $user_id) {
            return response()->json([
                'message' => '글을 삭제할 권한이 없습니다.'
            ], 403);
        }

        
        DB::table('gallery_posts')
        ->where('id', $post->id)
        ->where('user_id', $user_id)
        ->delete();


        return response()->json([
            'message' => '갤러리가 삭제되었습니다.'
        ], 200);
    }

/**
 * @OA\Put(
 *     path="/api/post/update",
 *     summary="게시글 수정",
 *     description="특정 갤러리의 게시글을 수정합니다.",
 *     tags={"Gallery Posts"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"gallery_id", "post_id", "user_id", "user_name", "title", "content"},
 *             @OA\Property(property="gallery_id", type="integer", example=1, description="갤러리 ID"),
 *             @OA\Property(property="post_id", type="integer", example=10, description="수정할 게시글의 ID"),
 *             @OA\Property(property="user_id", type="integer", example=5, description="게시글을 수정할 사용자 ID"),
 *             @OA\Property(property="user_name", type="string", example="user_name", description="사용자 이름"),
 *             @OA\Property(property="title", type="string", example="게시글 제목", description="게시글 제목"),
 *             @OA\Property(property="content", type="string", example="<p>게시글 내용</p>", description="게시글 내용")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="게시글 수정 완료",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="글 수정이 완료되었습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="권한이 없는 사용자",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="글을 수정할 권한이 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="게시글을 찾을 수 없음",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="해당 게시글을 찾을 수 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     )
 * )
 */

    public function postUpdate(Request $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'gallery_id' => 'required|numeric',
            'post_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'user_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('gallery_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('post_id')){
                return response()->json([
                    'error' => '게시글이 존재하지 않습니다.'
                ],422);
            }

            if($errors->has('user_id')){
                return response()->json([
                    'error' => '글 작성자만 지울 수 있습니다.'
                ],422);
            }
        }

        $gallery_id = $request->gallery_id;   
        $post_id = $request->post_id;
        $user_id = $request->user_id;

         //purifier를 통해서 content 내부의 위험요소 제거 (script, img의 src중 서버에 없는 src등)
         $content = $this->purifier->clean($request->input('content'));

        $post = DB::table('gallery_posts')
        ->where('gallery_id', $gallery_id)
        ->where('id', $post_id) 
        ->first();

        if (!$post) {
            return response()->json([
                'message' => '해당 게시글을 찾을 수 없습니다.'
            ], 404);
        }

        if ($post->user_id != $user_id) {
            return response()->json([
                'message' => '글을 수정할 권한이 없습니다.'
            ], 403);
        }

        DB::table('gallery_posts')
        ->where('id', $post_id)
        ->update([
            'title' => $request->title,
            'content' => $content,
            'updated_at' => now(),  
        ]);


        return response()->json([
            'message' => '글 수정이 완료되었습니다.'
        ], 200);
    }

}
