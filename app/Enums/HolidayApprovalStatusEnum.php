<?php

namespace App\Enums;

enum HolidayApprovalStatusEnum: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
