<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\UploadInterface;

class AnalyzeCommand
{
    public function __construct(
        private UploadInterface $upload,
    ) {
    }

    public function getUpload(): UploadInterface
    {
        return $this->upload;
    }
}
