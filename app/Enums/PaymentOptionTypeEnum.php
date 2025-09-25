<?php

namespace App\Enums;

enum PaymentOptionTypeEnum: string
{
    case UPFRONT_FULL = 'upfront_full';
    case PARTIAL_UPFRONT = 'partial_upfront';
    case AFTER_COMPLETION = 'after_completion';
}
