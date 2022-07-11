<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LockFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity(LockFileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LockFile implements LockFileInterface
{
    use DateTimeFieldsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $fileName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $originalFilename;

    public function __construct(string $fileName, string $originalFilename)
    {
        $this->fileName = $fileName;
        $this->originalFilename = $originalFilename;
    }

    public function getIdStrict(): int
    {
        Assert::integer($this->id, 'LockFile must be saved');

        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }
}
