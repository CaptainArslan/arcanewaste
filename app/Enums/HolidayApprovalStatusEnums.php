<?php

namespace App\Enums;

enum HolidayApprovalStatusEnums: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
