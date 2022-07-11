<?php

declare(strict_types=1);

namespace App\Dto;

class FileInfoDto
{
    public function __construct(
        private string $originalFileName,
        private string $fileName,
    ) {
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
