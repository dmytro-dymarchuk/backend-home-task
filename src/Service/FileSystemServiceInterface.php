<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\FileInfoDto;
use App\Entity\LockFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileSystemServiceInterface
{
    /**
     * Saves uploaded files to filesystem. Returns FileInfoDto including new generated file name.
     */
    public function upload(UploadedFile $uploadedFile): FileInfoDto;

    /**
     * Returns file path to concrete file.
     */
    public function getFilePath(LockFileInterface $lockFile): string;
}
