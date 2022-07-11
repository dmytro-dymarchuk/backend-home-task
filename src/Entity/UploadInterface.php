<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

interface UploadInterface
{
    public function getIdStrict(): int;

    /**
     * @return iterable<LockFileInterface>
     */
    public function getFiles(): iterable;

    public function getCreatedAt(): DateTime;
}
