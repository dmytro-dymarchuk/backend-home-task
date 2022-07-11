<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UploadRepository;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity(repositoryClass: UploadRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Upload implements UploadInterface
{
    use DateTimeFieldsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    /**
     * @var iterable<LockFileInterface>
     */
    #[ORM\OneToMany(mappedBy: 'upload', targetEntity: LockFile::class, cascade: ['persist', 'remove'])]
    private iterable $files;

    /**
     * @param iterable<LockFileInterface> $files
     */
    public function __construct(iterable $files)
    {
        $this->files = $files;
    }

    public function getIdStrict(): int
    {
        Assert::integer($this->id, 'Upload must be saved');

        return $this->id;
    }

    /**
     * @return iterable<LockFileInterface>
     */
    public function getFiles(): iterable
    {
        return $this->files;
    }
}
