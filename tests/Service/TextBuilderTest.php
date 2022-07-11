<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Service\TextBuilder;
use PHPUnit\Framework\TestCase;

class TextBuilderTest extends TestCase
{
    /**
     * @dataProvider buildSubjectDataProvider
     */
    public function testBuildSubject(TriggerEnum $triggerEnum, string $expectedResult): void
    {
        self::assertSame($expectedResult, (new TextBuilder())->buildSubject($triggerEnum));
    }

    public function buildSubjectDataProvider(): array
    {
        return [
            [
                TriggerEnum::IN_PROGRESS,
                'Your upload is in progress',
            ],
            [
                TriggerEnum::FAIL,
                'Your upload failed',
            ],
            [
                TriggerEnum::VULNERABILITIES_FOUND,
                'Found vulnerabilities more than expected',
            ],
        ];
    }

    /**
     * @dataProvider buildDescriptionDataProvider
     *
     * @param array<string> $fileNames
     * @param array<string> $expectedResults
     */
    public function testBuildDescription(int $id, array $fileNames, array $expectedResults): void
    {
        $files = [];
        foreach ($fileNames as $fileName) {
            $file = $this->getMockForAbstractClass(
                originalClassName: UploadInterface::class,
                mockedMethods: ['getOriginalFileName'],
            );
            $file->expects(self::once())->method('getOriginalFileName')->willReturn($fileName);
            $files[] = $file;
        }

        $upload = $this->getMockForAbstractClass(
            originalClassName: UploadInterface::class,
            mockedMethods: ['getIdStrict', 'getFiles'],
        );
        $upload->expects(self::once())->method('getIdStrict')->willReturn($id);
        $upload->expects(self::once())->method('getFiles')->willReturn($files);

        $description = (new TextBuilder())->buildDescription($upload);
        foreach ($expectedResults as $expectedResult) {
            self::assertStringContainsString($expectedResult, $description);
        }
    }

    public function buildDescriptionDataProvider(): array
    {
        return [
            [
                1,
                ['file1', 'file2'],
                [
                    'Upload ID 1, created at - ',
                    'Files: file1, file2',
                ],
            ],
            [
                2,
                ['file3'],
                [
                    'Upload ID 2, created at - ',
                    'Files: file3',
                ],
            ],
        ];
    }
}
