<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\FileInfoDto;
use App\Entity\LockFile;
use App\Entity\Upload;
use App\Entity\UploadInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Upload>
 */
class UploadRepository extends ServiceEntityRepository implements UploadRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Upload::class);
    }

    /**
     * @param array<FileInfoDto> $fileInfoDtos
     */
    public function saveUpload(array $fileInfoDtos): UploadInterface
    {
        $lockFiles = [];

        foreach ($fileInfoDtos as $fileInfoDto) {
            $lockFiles[] = new LockFile($fileInfoDto->getFileName(), $fileInfoDto->getOriginalFileName());
        }

        $upload = new Upload($lockFiles);
        $this->getEntityManager()->persist($upload);
        $this->getEntityManager()->flush();

        return $upload;
    }
}
