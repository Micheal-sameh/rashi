<?php

namespace App\DTOs;

use App\Attributes\HasEmptyPlaceholders;

#[HasEmptyPlaceholders]
class UserLoginDTO extends DTO
{
    public ?string $name;
    public ?string $email;
    public ?string $phone;
    public ?string $membership_code;
    public ?string $group;
    public ?string $password;

    public function __construct(
        string $name = parent::STRING,
        string $email = parent::STRING,
        string $phone = parent::STRING,
        string $membership_code = parent::STRING,
        string $group = parent::STRING,
        string $password = parent::STRING,
    ) {
        parent::__construct(compact(...$this->getParameterList()));
    }
}
