<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Services\UserService;

class UserController extends BaseController
{
    public function __construct(protected UserService $userService) {}
}
