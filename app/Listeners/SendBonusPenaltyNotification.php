<?php

namespace App\Listeners;

use App\Enums\BonusPenaltyType;
use App\Events\BonusPenaltyCreated;
use App\Models\BonusPenalty;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBonusPenaltyNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    public function handle(BonusPenaltyCreated $event)
    {
        $bonusPenalty = $event->bonusPenalty;

        $typeString = $bonusPenalty->type == BonusPenaltyType::PENALTY ? 'penalty' : 'bonus';
        $typeString = $bonusPenalty->type == BonusPenaltyType::PENALTY ? 'penalty' : 'bonus';
        $title = $bonusPenalty->type == (BonusPenaltyType::BONUS || BonusPenaltyType::WELCOME_BONUS) ? __('messages.bonus_awarded') : __('messages.penalty_applied');
        $message = match ($bonusPenalty->type) {
            BonusPenaltyType::WELCOME_BONUS => __('messages.welcome_bonus_message'),
            BonusPenaltyType::BONUS => __('messages.bonus_message', ['points' => $bonusPenalty->points, 'reason' => $bonusPenalty->reason]),
            BonusPenaltyType::PENALTY => __('messages.penalty_message', ['points' => $bonusPenalty->points, 'reason' => $bonusPenalty->reason]),
        };

        $this->notificationRepository->createNotificationForUser(
            $bonusPenalty->user_id,
            $title,
            $message,
            $typeString,
            BonusPenalty::class,
            $bonusPenalty->id,
            [
                'points' => $bonusPenalty->points,
                'reason' => $bonusPenalty->reason,
                'type' => $bonusPenalty->type,
            ]
        );
    }
}
