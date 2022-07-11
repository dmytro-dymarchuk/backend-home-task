<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;

interface TextBuilderInterface
{
    /**
     * Returns the subject of the trigger.
     */
    public function buildSubject(TriggerEnum $triggerEnum): string;

    /**
     * Returns description of Upload.
     */
    public function buildDescription(UploadInterface $upload): string;
}
