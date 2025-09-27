<?php

namespace App\Enums;

enum FinixOnboardingStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case PROVISIONING = 'provisioning';
    case FAILED = 'failed';
}
