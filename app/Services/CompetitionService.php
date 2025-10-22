<?php

namespace App\Services;

use App\Repositories\CompetitionRepository;
use Illuminate\Support\Facades\DB;

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

    public function update($id, $input, $image)
    {
        return $this->competitionRepository->update($id, $input, $image);
    }

    public function cancel($id)
    {
        return $this->competitionRepository->cancel($id);
    }

    public function dropdown()
    {
        return $this->competitionRepository->dropdown();
    }

    public function changeStatus($id)
    {
        DB::beginTransaction();
        $data = $this->competitionRepository->changeStatus($id);
        DB::commit();

        return $data;
    }

    public function getUsersForCompetition($competition)
    {
        return $this->competitionRepository->getUsersForCompetition($competition);
    }

    public function getUserStatsForQuiz($quiz)
    {
        $userStats = [];
        foreach ($quiz->questions as $question) {
            foreach ($question->userAnswers as $userAnswer) {
                $userId = $userAnswer->user_id;
                if (! isset($userStats[$userId])) {
                    $userStats[$userId] = [
                        'name' => $userAnswer->user->name,
                        'total_correct' => 0,
                        'total_points' => 0,
                        'total_questions' => 0,
                    ];
                }
                $userStats[$userId]['total_questions']++;
                if ($userAnswer->answer->is_correct) {
                    $userStats[$userId]['total_correct']++;
                }
                $userStats[$userId]['total_points'] += $userAnswer->points;
            }
        }

        return $userStats;
    }
}
