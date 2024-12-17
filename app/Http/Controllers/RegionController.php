<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/regions",
 *     summary="지역 목록을 가져오는 API",
 *     tags={"Regions"},
 *     @OA\Response(
 *         response=200,
 *         description="성공적으로 지역 목록을 가져옴",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Seoul"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-17T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-17T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="지역 데이터를 찾을 수 없음",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="No regions found")
 *         )
 *     )
 * )
 */
    public function regions()
    {
        $regions = DB::table('regions')->get();

        if ($regions->isEmpty()) {
            return response()->json([
                'message' => '서버의 접속이 원활하지 않습니다'
            ], 500); 
        }


        return response()->json($regions);
    }
}