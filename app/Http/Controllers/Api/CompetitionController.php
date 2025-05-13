<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CompetitionResource;
use App\Services\CompetitionService;

class CompetitionController extends BaseController
{
    public function __construct(protected CompetitionService $competitionService) {}

    public function index()
    {
        $competitions = $this->competitionService->index();

        return $this->respondResource(CompetitionResource::collection($competitions));
    }
}
