<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PointHistory;
use App\Models\Quiz;
use App\Models\Returns;
use App\Models\Reward;
use App\Models\RewardHistory;
use Illuminate\Support\Facades\Auth;

/**
 * Service for managing point and reward history records.
 */
class HistoryService
{
    public function addPointHistory($data)
    {
        if ($data['subject'] instanceof Quiz) {
            $type = get_class($data['subject']);
            $subject_id = $data['subject']->id;
            $points = $data['score'];
            $score = $data['score'];
        } else {
            $type = get_class($data);
            $subject_id = $data->id;
            $points = ($data instanceof Order) ? -1 * $data->points : $data->points;
            $score = 0;
        }

        $user = Auth::user();
        PointHistory::create([
            'user_id' => $user->id,
            'amount' => abs($points),
            'points' => $user->points + $points,
            'score' => $user->score + $score,
            'subject_id' => $subject_id,
            'subject_type' => $type,
        ]);
    }

    public function addRewardHistory($object, $quantity = null)
    {
        $reward_id = match (get_class($object)) {
            Reward::class => $object->id,
            Order::class => $object->reward->id,
            Returns::class => $object->reward->id,
            default => null
        };

        RewardHistory::create([
            'quantity' => $quantity ?: $object->quantity,
            'points' => $object->points,
            'subject_id' => $object->id,
            'subject_type' => get_class($object),
            'created_by' => Auth::id(),
            'reward_id' => $reward_id,
        ]);
    }
}
