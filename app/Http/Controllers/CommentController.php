<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use Mews\Purifier\Purifier;


class CommentController extends Controller
{
  
    protected $purifier;

    public function __construct(Purifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
 * @OA\Get(
 *     path="/api/regions/gallery/post/comments",
 *     summary="특정 게시물에 대한 코멘트 조회",
 *     description="주어진 post_id를 기반으로 특정 게시물의 코멘트를 조회합니다.",
 *     operationId="viewComment",
 *     tags={"Comments"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"post_id"},
 *             @OA\Property(property="post_id", type="integer", description="게시물의 ID")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="코멘트가 존재하는 경우 코멘트를 반환합니다.",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="message", type="string", example="코멘트가 존재하지 않습니다."),
 *                     @OA\Property(property="comment_status", type="string", example="0")
 *                 ),
 *                 @OA\Schema(
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         properties={
 *                             @OA\Property(property="id", type="integer", description="코멘트 ID"),
 *                             @OA\Property(property="post_id", type="integer", description="게시물 ID"),
 *                             @OA\Property(property="user_id", type="integer", description="코멘트를 작성한 유저 ID"),
 *                             @OA\Property(property="content", type="string", description="코멘트 내용"),
 *                             @OA\Property(property="created_at", type="string", format="date-time", description="코멘트 작성 시간")
 *                         }
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="잘못된 요청 (post_id가 누락되었거나 잘못되었습니다)",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     )
 * )
 */

    public function viewComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|numeric',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('post_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }
        }

        $comments = DB::table('comments')
        ->where('post_id', $request->post_id)
        ->get();

        if ($comments->isEmpty()) {
            return response()->json([
                'message' => '코멘트가 존재하지 않습니다.',
                'comment_status' => '0'
            ], 200); 
        }

        return response()->json($comments);
    }

    /**
 * @OA\Post(
 *     path="/api/regions/gallery/post/comments",
 *     summary="새로운 코멘트 추가",
 *     description="새로운 코멘트를 추가합니다. 코멘트에는 게시물 ID, 사용자 ID, 내용, 비밀번호 등의 정보가 필요합니다.",
 *     operationId="commentAdd",
 *     tags={"Comments"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"post_id", "user_id", "content", "password"},
 *             @OA\Property(property="post_id", type="integer", description="게시물의 ID"),
 *             @OA\Property(property="user_id", type="integer", description="사용자 ID"),
 *             @OA\Property(property="user_name", type="string", description="사용자 이름 (선택 사항)"),
 *             @OA\Property(property="content", type="string", description="코멘트 내용"),
 *             @OA\Property(property="password", type="string", description="코멘트 비밀번호"),
 *             @OA\Property(property="parent_comment_id", type="integer", description="부모 코멘트 ID (선택 사항)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="코멘트가 성공적으로 추가되었습니다.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="게시글 생성 완료")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="입력 값에 오류가 있을 경우 발생하는 오류.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     )
 * )
 */

    public function commentAdd(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'content' => 'required',
            'password' => 'required|string',
            'parent_comment_id' => 'numeric'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('post_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('user_id')){
                return response()->json([
                    'error' => '로그인 후 이용해주세요.'
                ],422);
            }


            if($errors->has('content')){
                return response()->json([
                    'error' => '내용을 입력해주세요.'
                ],422);
            }

            if($errors->has('password')){
                return response()->json([
                    'error' => '비밀번호가 정확하지 않습니다.'
                ],422);
            }
        }

        //purifier를 통해서 content 내부의 위험요소 제거 (script, img의 src중 서버에 없는 src등)
        $content = $this->purifier->clean($request->input('content'));

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'username' => $request->user_name,
            'content' => $content,
            'password' => $request->password,
            'parent_comment_id' => $request->parent_comment_id
        ]);

        return response()->json([
            'message' => '게시글 생성 완료' 
        ], 201);

    }

    /**
 * @OA\Delete(
 *     path="/api/regions/gallery/post/comments",
 *     summary="코멘트 삭제",
 *     description="주어진 코멘트 ID와 비밀번호를 이용해 특정 코멘트를 삭제합니다. 코멘트 작성자만 삭제할 수 있습니다.",
 *     operationId="commentDelete",
 *     tags={"Comments"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"post_id", "comment_id", "user_id", "password"},
 *             @OA\Property(property="post_id", type="integer", description="게시물의 ID"),
 *             @OA\Property(property="comment_id", type="integer", description="삭제할 코멘트의 ID"),
 *             @OA\Property(property="user_id", type="integer", description="사용자 ID"),
 *             @OA\Property(property="password", type="string", description="코멘트 비밀번호")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="코멘트가 성공적으로 삭제되었습니다.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="코멘트가 삭제되었습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="해당 코멘트를 찾을 수 없는 경우",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="해당 코멘트를 찾을 수 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="삭제 권한이 없는 경우",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="글을 삭제할 권한이 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="잘못된 요청이 있을 경우 발생하는 오류",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=402,
 *         description="비밀번호가 올바르지 않은 경우 발생하는 오류",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="비밀번호가 올바르지 않습니다.")
 *         )
 *     )
 * )
 */

    public function commentDelete(Request $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|numeric',
            'comment_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'password' => 'required|string'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('post_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('comment_id')){
                return response()->json([
                    'error' => '코멘트가 존재하지 않습니다.'
                ],422);
            }

            if($errors->has('user_id')){
                return response()->json([
                    'error' => '글 작성자만 지울 수 있습니다.'
                ],422);
            }

            if($errors->has('password')){
                return response()->json([
                    'error' => '비밀번호가 올바르지 않습니다.'
                ],422);
            }
        }

 
        $comment_id = $request->comment_id;  
        $user_id = $request->user_id;
        $password = $request->password;

        $comment = DB::table('comments')
        ->where('id', $comment_id)
        ->first();

        if (!$comment) {
            return response()->json([
                'message' => '해당 코멘트를 찾을 수 없습니다.'
            ], 404);
        }

        if ($comment->user_id != $user_id) {
            return response()->json([
                'message' => '글을 삭제할 권한이 없습니다.'
            ], 403);
        }

        if($comment->password != $password){
            return response()->json([
                'message' => '비밀번호가 올바르지 않습니다.'
            ], 402);
        }

        
        DB::table('comments')
        ->where('id', $comment_id)
        ->delete();


        return response()->json([
            'message' => '코멘트가 삭제되었습니다.'
        ], 200);

    }

    /**
 * @OA\Put(
 *     path="/api/regions/gallery/post/comments",
 *     summary="코멘트 수정",
 *     description="주어진 코멘트 ID와 비밀번호를 이용해 특정 코멘트를 수정합니다. 코멘트 작성자만 수정할 수 있습니다.",
 *     operationId="commentUpdate",
 *     tags={"Comments"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"post_id", "comment_id", "user_id", "password", "content"},
 *             @OA\Property(property="post_id", type="integer", description="게시물의 ID"),
 *             @OA\Property(property="comment_id", type="integer", description="수정할 코멘트의 ID"),
 *             @OA\Property(property="user_id", type="integer", description="사용자 ID"),
 *             @OA\Property(property="password", type="string", description="코멘트 비밀번호"),
 *             @OA\Property(property="content", type="string", description="수정할 코멘트 내용")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="코멘트가 성공적으로 수정되었습니다.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="글 수정이 완료되었습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="해당 코멘트를 찾을 수 없는 경우",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="해당 게시글을 찾을 수 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="수정 권한이 없는 경우",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="글을 수정할 권한이 없습니다.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="잘못된 요청이 있을 경우 발생하는 오류",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="부적절한 접근입니다.")
 *         )
 *     )
 * )
 */

    public function commentUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|numeric',
            'comment_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'password' => 'required|string'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();

            if($errors->has('post_id')){
                return response()->json([
                    'error' => '부적절한 접근입니다.'
                ],422);
            }

            if($errors->has('comment_id')){
                return response()->json([
                    'error' => '코멘트가 존재하지 않습니다.'
                ],422);
            }

            if($errors->has('user_id')){
                return response()->json([
                    'error' => '글 작성자만 변경할 수 있습니다.'
                ],422);
            }

            if($errors->has('password')){
                return response()->json([
                    'error' => '비밀번호가 올바르지 않습니다.'
                ],422);
            }
        }
 
        $comment_id = $request->comment_id;
        $user_id = $request->user_id;
        $password = $request->password;


        $comment = DB::table('comments')
        ->where('id', $comment_id) 
        ->first();

        if (!$comment) {
            return response()->json([
                'message' => '해당 게시글을 찾을 수 없습니다.'
            ], 404);
        }

        if ($comment->user_id != $user_id) {
            return response()->json([
                'message' => '글을 수정할 권한이 없습니다.'
            ], 403);
        }

        if ($comment->password != $password) {
            return response()->json([
                'message' => '글을 수정할 권한이 없습니다.'
            ], 403);
        }
         //purifier를 통해서 content 내부의 위험요소 제거 (script, img의 src중 서버에 없는 src등)
         $content = $this->purifier->clean($request->input('content'));       

        DB::table('comments')
        ->where('id', $comment_id)
        ->update([
            'content' => $content,
            'updated_at' => now(),  
        ]);


        return response()->json([
            'message' => '글 수정이 완료되었습니다.'
        ], 200);
    }


}