<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
}
