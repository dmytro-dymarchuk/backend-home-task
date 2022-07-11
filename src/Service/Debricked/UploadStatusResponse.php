<?php

declare(strict_types=1);

namespace App\Service\Debricked;

class UploadStatusResponse
{
    public function __construct(
        private UploadStatusEnum $statusEnum,
        private int $vulnerabilitiesCount = 0
    ) {
    }

    public function getStatusEnum(): UploadStatusEnum
    {
        return $this->statusEnum;
    }

    public function getVulnerabilitiesCount(): int
    {
        return $this->vulnerabilitiesCount;
    }
}
