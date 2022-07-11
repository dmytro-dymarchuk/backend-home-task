<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;

class TextBuilder implements TextBuilderInterface
{
    public function buildSubject(TriggerEnum $triggerEnum): string
    {
        return match ($triggerEnum) {
            TriggerEnum::IN_PROGRESS => 'Your upload is in progress',
            TriggerEnum::FAIL => 'Your upload failed',
            TriggerEnum::VULNERABILITIES_FOUND => 'Found vulnerabilities more than expected',
        };
    }

    public function buildDescription(UploadInterface $upload): string
    {
        $files = [];

        foreach ($upload->getFiles() as $file) {
            $files[] = $file->getOriginalFilename();
        }

        return sprintf(
            "Upload ID %s, created at - %s\nFiles: %s",
            $upload->getIdStrict(),
            $upload->getCreatedAt()->format('c'),
            implode(', ', $files)
        );
    }
}
