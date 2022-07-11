<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadServiceInterface
{
    /**
     * Handles uploaded files.
     *
     * @param array<UploadedFile> $uploadedFiles
     */
    public function saveFromUploads(array $uploadedFiles): void;
}
