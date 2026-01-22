<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InfoVideoResource;
use App\Models\InfoVideo;

class InfoVideoController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/info-videos",
     *     summary="Get all visible info videos",
     *     tags={"Info Videos"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of visible info videos ordered by rank",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Introduction Video"),
     *                 @OA\Property(property="link", type="string", example="https://youtube.com/watch?v=example"),
     *                 @OA\Property(property="appear", type="integer", example=1),
     *                 @OA\Property(property="appear_status", type="string", example="Appear"),
     *                 @OA\Property(property="media", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="url", type="string", example="https://example.com/media/video.mp4"),
     *                     @OA\Property(property="name", type="string", example="video.mp4")
     *                 ))
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $infoVideos = InfoVideo::where('appear', 1)->orderBy('rank', 'asc')->get();

        return $this->apiResponse(InfoVideoResource::collection($infoVideos));
    }
}
