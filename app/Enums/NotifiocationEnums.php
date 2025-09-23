<?php

namespace App\Enums;

enum NotifiocationEnums: string
{
    case COMPANY_ONBOARDING_COMPLETED = 'company_onboarding_completed';
    case COMPANY_ONBOARDING_FAILED = 'company_onboarding_failed';
    case COMPANY_ONBOARDING_STARTED = 'company_onboarding_started';
    case COMPANY_ONBOARDING_IN_PROGRESS = 'company_onboarding_in_progress';
    case COMPANY_ONBOARDING_PENDING = 'company_onboarding_pending';
    case COMPANY_ONBOARDING_PROVISIONING = 'company_onboarding_provisioning';
    case COMPANY_ONBOARDING_REJECTED = 'company_onboarding_rejected';
}
