<?php

namespace App\DTOs;

use App\Attributes\HasEmptyPlaceholders;

#[HasEmptyPlaceholders]
class RewardCreateDTO extends DTO
{
    public string $name;

    public int $quantity;

    public int $points;

    public ?int $status;

    public function __construct(
        string $name = parent::STRING,
        int $quantity = parent::INT,
        int $points = parent::INT,
        int $status = parent::INT,
    ) {
        parent::__construct(compact(...$this->getParameterList()));
    }
}
