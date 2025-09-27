<?php

namespace App\Enums;

enum RecurrenceTypeEnum: string
{
    case NONE = 'none';
    case WEEKLY = 'weekly';
    case YEARLY = 'yearly';
}
