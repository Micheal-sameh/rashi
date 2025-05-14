<?php

namespace App\DTOs;

use App\Attributes\HasEmptyPlaceholders;

#[HasEmptyPlaceholders]
class QuestionCreateDTO extends DTO
{
    public ?string $question;
    public ?int $points;
    public ?int $quiz_id;
    public ?int $correct;
    public ?array $answers;

    public function __construct(
        string $question = parent::STRING,
        int $points = parent::INT,
        int $quiz_id = parent::INT,
        int $correct = parent::INT,
        array $answers = parent::ARRAY,
    ) {
        parent::__construct(compact(...$this->getParameterList()));
    }
}
