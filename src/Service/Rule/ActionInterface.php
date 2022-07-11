<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;

interface ActionInterface
{
    /**
     * Sends notifications about the trigger.
     */
    public function notify(TriggerEnum $triggerEnum, UploadInterface $upload): void;
}
