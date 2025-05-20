<?php

namespace App\DTOs;

use App\Attributes\HasEmptyPlaceholders;

#[HasEmptyPlaceholders]
class UsersFilterDTO extends DTO
{
    public ?string $name;
    public ?int $group_id;
    public ?string $sort_by;
    public ?string $direction;

    public function __construct(
        string $name = parent::STRING,
        int $group_id = parent::INT,
        string $sort_by = parent::STRING,
        string $direction = parent::STRING,
    ) {
        parent::__construct(compact(...$this->getParameterList()));
    }
}
