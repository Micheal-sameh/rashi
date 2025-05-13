<?php

namespace App\Services;

use App\Repositories\CompetitionRepository;

class CompetitionService
{
    public function __construct(protected CompetitionRepository $competitionRepository) {}

    public function index()
    {
        $competitions = $this->competitionRepository->index();

        return $competitions;
    }

    public function show($id)
    {
        $competition = $this->competitionRepository->show($id);

        return $competition;
    }

    public function store($input, $image)
    {
        return $this->competitionRepository->store($input, $image);
    }
}
