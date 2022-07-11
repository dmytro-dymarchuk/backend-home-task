<?php

declare(strict_types=1);

namespace App\Tests\Dto;

use App\Dto\FileInfoDto;
use PHPUnit\Framework\TestCase;

class FileInfoDtoTest extends TestCase
{
    public function testGetters(): void
    {
        $originalFileName = 'original_file_name';
        $fileName = 'generated_file_name';
        $fileInfoDto = new FileInfoDto($originalFileName, $fileName);
        self::assertSame($fileName, $fileInfoDto->getFileName());
        self::assertSame($originalFileName, $fileInfoDto->getOriginalFileName());
    }
}
