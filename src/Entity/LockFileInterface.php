<?php

declare(strict_types=1);

namespace App\Entity;

interface LockFileInterface
{
    public function getFileName(): string;

    public function getOriginalFilename(): string;
}
