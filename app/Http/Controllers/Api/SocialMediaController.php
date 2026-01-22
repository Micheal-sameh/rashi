<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SocialMediaResource;
use App\Models\SocialMedia;

class SocialMediaController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/social-media",
     *     summary="Get all social media platforms",
     *     tags={"Social Media"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of social media platforms",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Facebook"),
     *                 @OA\Property(property="link", type="string", example="https://www.facebook.com/example"),
     *                 @OA\Property(property="icon", type="string", example="fab fa-facebook")
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $socialMedia = SocialMedia::all();

        return $this->apiResponse(SocialMediaResource::collection($socialMedia));
    }
}
