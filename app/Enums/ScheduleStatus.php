<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
