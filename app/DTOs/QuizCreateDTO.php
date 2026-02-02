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

    public ?string $help;

    public function __construct(
        string $name = parent::STRING,
        string $date = parent::STRING,
        ?int $competition_id = null,
        ?array $questions = null,
        string $help = parent::STRING, )
    {
        // Set defaults for null values to match parent's placeholders
        $competition_id = $competition_id ?? parent::INT;
        $questions = $questions ?? parent::ARRAY;
        parent::__construct(compact(...$this->getParameterList()));
    }
}
