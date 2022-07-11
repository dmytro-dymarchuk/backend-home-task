<?php

declare(strict_types=1);

namespace App\Message;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;

class SendToSlackCommand
{
    public function __construct(
        public TriggerEnum $triggerEnum,
        public UploadInterface $upload,
    ) {
    }

    public function getTriggerEnum(): TriggerEnum
    {
        return $this->triggerEnum;
    }

    public function getUpload(): UploadInterface
    {
        return $this->upload;
    }
}
