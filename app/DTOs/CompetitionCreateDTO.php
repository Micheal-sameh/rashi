<?php

namespace App\DTOs;

use App\Attributes\HasEmptyPlaceholders;

#[HasEmptyPlaceholders]
class CompetitionCreateDTO extends DTO
{
    public ?string $name;

    public ?string $start_at;

    public ?string $end_at;

    public ?array $groups;

    public function __construct(
        string $name = parent::STRING,
        string $start_at = parent::STRING,
        string $end_at = parent::STRING,
        array $groups = parent::ARRAY,
    ) {
        parent::__construct(compact(...$this->getParameterList()));
    }
}
