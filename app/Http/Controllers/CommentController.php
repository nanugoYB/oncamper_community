<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\GalleryPost;
use Mews\Purifier\Purifier;


class CommentController extends Controller
{
  
    public function viewComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|numeric',
        ]);
   

        $comments = DB::table('comments')
        ->where('post_id', $request->post_id)
        ->get();

        if ($comments->isEmpty()) {
            return response()->json([
                'message' => '서버의 접속이 원활하지 않습니다'
            ], 500); 
        }


        return response()->json($regions);
    }
}