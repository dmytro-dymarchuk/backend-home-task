<?php

declare(strict_types=1);

namespace App\Component\Enum;

enum TriggerEnum: string
{
    case VULNERABILITIES_FOUND = 'vulnerabilities_found';
    case IN_PROGRESS = 'in_progress';
    case FAIL = 'fail';
}
