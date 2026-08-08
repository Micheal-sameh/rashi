<?php

namespace App\DTOs;

use App\Enums\ScheduleFrequency;

final readonly class ScheduleReportDTO
{
    /**
     * @param  array<int, string>  $recipients
     */
    public function __construct(
        public int $reportId,
        public ScheduleFrequency $frequency,
        public string $time,
        public ?int $dayOfWeek,
        public ?int $dayOfMonth,
        public array $recipients,
        public bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            reportId: (int) $data['report_id'],
            frequency: ScheduleFrequency::from($data['frequency']),
            time: $data['time'],
            dayOfWeek: isset($data['day_of_week']) ? (int) $data['day_of_week'] : null,
            dayOfMonth: isset($data['day_of_month']) ? (int) $data['day_of_month'] : null,
            recipients: $data['recipients'] ?? [],
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }
}
