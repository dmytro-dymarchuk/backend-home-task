<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\FileInfoDto;
use App\Entity\LockFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSystemService implements FileSystemServiceInterface
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $uploadedFile): FileInfoDto
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $uploadedFile->move($this->targetDirectory, $fileName);

        return new FileInfoDto($uploadedFile->getClientOriginalName(), $fileName);
    }

    public function getFilePath(LockFileInterface $lockFile): string
    {
        return rtrim($this->targetDirectory, DIRECTORY_SEPARATOR)
            .DIRECTORY_SEPARATOR
            .$lockFile->getFileName();
    }
}
