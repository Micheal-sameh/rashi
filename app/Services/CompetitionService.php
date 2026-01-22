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

    public function setStatus($id, $status)
    {
        DB::beginTransaction();
        $this->competitionRepository->setStatus($id, $status);
        DB::commit();
    }

    public function getUsersForCompetition($competition, $groupId = null)
    {
        return $this->competitionRepository->getUsersForCompetition($competition, $groupId);
    }

    public function getUserStatsForQuiz($quiz, $userIds = [])
    {
        // Ensure relationships are loaded to prevent N+1
        if (! $quiz->relationLoaded('questions')) {
            $quiz->load(['questions.userAnswers.user', 'questions.userAnswers.answer']);
        }

        $userStats = [];
        foreach ($quiz->questions as $question) {
            foreach ($question->userAnswers as $userAnswer) {
                $userId = $userAnswer->user_id;

                // Skip if filtering by user IDs and this user is not in the list
                if (! empty($userIds) && ! in_array($userId, $userIds)) {
                    continue;
                }

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
