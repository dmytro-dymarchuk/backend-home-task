<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LockFile;
use PHPUnit\Framework\TestCase;

class LockFileTest extends TestCase
{
    public function testGetters(): void
    {
        $originalFileName = 'original_file_name';
        $fileName = 'generated_file_name';
        $lockFile = new LockFile($fileName, $originalFileName);
        self::assertSame($originalFileName, $lockFile->getOriginalFilename());
        self::assertSame($fileName, $lockFile->getFileName());
    }
}
