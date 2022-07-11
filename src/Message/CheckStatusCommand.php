<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\UploadInterface;

class CheckStatusCommand
{
    public function __construct(
        private UploadInterface $upload,
        private int $ciUploadId,
    ) {
    }

    public function getUpload(): UploadInterface
    {
        return $this->upload;
    }

    public function getCiUploadId(): int
    {
        return $this->ciUploadId;
    }
}
