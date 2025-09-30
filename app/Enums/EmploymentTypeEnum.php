<?php

namespace App\Enums;

enum EmploymentTypeEnum: string
{
    // full_time, part_time, contract
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
}
