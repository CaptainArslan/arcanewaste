<?php

namespace App\Enums;

enum HolidayApprovalStatusEnum: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
