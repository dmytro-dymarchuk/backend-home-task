<?php

declare(strict_types=1);

namespace App\Component\Enum;

enum ActionEnum: string
{
    case SEND_EMAIL = 'send_email';
    case SEND_TO_SLACK = 'send_to_slack';
}
