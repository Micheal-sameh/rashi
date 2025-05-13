<?php

namespace App\DTOs;

use App\Attributes\HasEmptyPlaceholders;

#[HasEmptyPlaceholders]
class QuizCreateDTO extends DTO
{
    public ?string $name;
    public ?string $date;
    public ?int $competition_id;
    public ?array $questions;

    public function __construct(
        string $name = parent::STRING,
        string $date = parent::STRING,
        int $competition_id = parent::INT,
        array $questions = parent::ARRAY,
    ) {
        parent::__construct(compact(...$this->getParameterList()));
    }
}
