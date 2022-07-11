<?php

declare(strict_types=1);

namespace App\Service;

use App\Message\AnalyzeCommand;
use App\Repository\UploadRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

class UploadService implements UploadServiceInterface
{
    public function __construct(
        private FileSystemServiceInterface $fileSystemService,
        private UploadRepositoryInterface $uploadRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @param array<UploadedFile> $uploadedFiles
     */
    public function saveFromUploads(array $uploadedFiles): void
    {
        $files = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $files[] = $this->fileSystemService->upload($uploadedFile);
        }

        $upload = $this->uploadRepository->saveUpload($files);

        $this->messageBus->dispatch(new AnalyzeCommand($upload));
    }
}
