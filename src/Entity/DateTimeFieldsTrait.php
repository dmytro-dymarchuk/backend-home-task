<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait DateTimeFieldsTrait
{
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $updatedAt = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $datetime = new DateTime();
        $this->createdAt ??= $datetime;
        $this->updatedAt = $datetime;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): DateTime
    {
        if (null === $this->createdAt) {
            throw new \LogicException('CreatedAt was not set');
        }

        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        if (null === $this->updatedAt) {
            throw new \LogicException('UpdatedAt was not set');
        }

        return $this->updatedAt;
    }
}
