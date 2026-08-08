<?php

namespace App\DTOs;

final readonly class ReportFilterDTO
{
    public function __construct(
        public string $column,
        public string $operator,
        public mixed $value,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            column: $data['column'],
            operator: $data['operator'],
            value: $data['value'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'column' => $this->column,
            'operator' => $this->operator,
            'value' => $this->value,
        ];
    }
}
