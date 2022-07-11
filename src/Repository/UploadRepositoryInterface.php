<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\FileInfoDto;
use App\Entity\UploadInterface;

interface UploadRepositoryInterface
{
    /**
     * Saves upload information to storage.
     *
     * @param array<FileInfoDto> $fileInfoDtos
     */
    public function saveUpload(array $fileInfoDtos): UploadInterface;
}
