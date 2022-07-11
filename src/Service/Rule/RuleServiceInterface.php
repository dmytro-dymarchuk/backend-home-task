<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;

interface RuleServiceInterface
{
    /**
     * Runs actions for trigger.
     */
    public function trigger(TriggerEnum $triggerEnum, UploadInterface $upload): void;
}
