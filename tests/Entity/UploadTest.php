<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LockFile;
use App\Entity\Upload;
use PHPUnit\Framework\TestCase;

class UploadTest extends TestCase
{
    public function testGetters(): void
    {
        $files = [$this->getMockBuilder(LockFile::class)->disableOriginalConstructor()->getMock()];
        $upload = new Upload($files);
        self::assertSame($files, $upload->getFiles());
    }
}
