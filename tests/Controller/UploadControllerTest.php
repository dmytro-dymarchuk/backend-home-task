<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Component\ConstraintViolationListNormalizerInterface;
use App\Controller\UploadController;
use App\Dto\InputUploadDto;
use App\Service\UploadServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadControllerTest extends TestCase
{
    public function testUploadSuccessful(): void
    {
        $files = [$this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock()];
        $fileBag = new FileBag();
        $fileBag->add($files);
        $request = new Request();
        $request->files = $fileBag;

        $denormalizerMock = $this->getDenormalizerMock(['denormalize']);
        $inputUploadDto = new InputUploadDto($files);
        $denormalizerMock->expects(self::once())->method('denormalize')
            ->with($files)
            ->willReturn($inputUploadDto);

        $constraintViolationListNormalizerMock = $this->getConstraintViolationListNormalizerMock([]);

        $validatorMock = $this->getValidatorMock(['validate']);
        $constraintViolationList = new ConstraintViolationList();
        $validatorMock->expects(self::once())->method('validate')
            ->with($inputUploadDto)
            ->willReturn($constraintViolationList);

        $uploadServiceMock = $this->getUploadServiceMock(['saveFromUploads']);
        $uploadServiceMock->expects(self::once())->method('saveFromUploads')
            ->with($files);

        $response = (new UploadController(
            $constraintViolationListNormalizerMock,
            $denormalizerMock,
            $validatorMock,
            $uploadServiceMock,
        ))->upload($request);

        self::assertSame(204, $response->getStatusCode());
    }

    public function testUploadWithViolations(): void
    {
        $files = [$this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock()];
        $fileBag = new FileBag();
        $fileBag->add($files);
        $request = new Request();
        $request->files = $fileBag;

        $denormalizerMock = $this->getDenormalizerMock(['denormalize']);
        $inputUploadDto = new InputUploadDto($files);
        $denormalizerMock->expects(self::once())->method('denormalize')
            ->with($files)
            ->willReturn($inputUploadDto);

        $constraintViolationListMock = $this->getMockBuilder(ConstraintViolationList::class)
            ->onlyMethods(['count'])
            ->disableOriginalConstructor()
            ->getMock();
        $constraintViolationListMock->expects(self::once())->method('count')->willReturn(1);

        $constraintViolationListNormalizerMock = $this->getConstraintViolationListNormalizerMock(['normalize']);
        $normalizedData = ['normalized_data'];
        $constraintViolationListNormalizerMock->expects(self::once())
            ->method('normalize')
            ->with($constraintViolationListMock)
            ->willReturn($normalizedData);

        $validatorMock = $this->getValidatorMock(['validate']);
        $validatorMock->expects(self::once())->method('validate')
            ->with($inputUploadDto)
            ->willReturn($constraintViolationListMock);

        $uploadServiceMock = $this->getUploadServiceMock();

        $response = (new UploadController(
            $constraintViolationListNormalizerMock,
            $denormalizerMock,
            $validatorMock,
            $uploadServiceMock,
        ))->upload($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(json_encode($normalizedData), $response->getContent());
    }

    /**
     * @param array<string> $methods
     *
     * @return ConstraintViolationListNormalizerInterface|MockObject
     */
    private function getConstraintViolationListNormalizerMock(array $methods = []): ConstraintViolationListNormalizerInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: ConstraintViolationListNormalizerInterface::class,
            mockedMethods: $methods,
        );
    }

    /**
     * @param array<string> $methods
     *
     * @return DenormalizerInterface|MockObject
     */
    private function getDenormalizerMock(array $methods = []): DenormalizerInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: DenormalizerInterface::class,
            mockedMethods: $methods,
        );
    }

    /**
     * @param array<string> $methods
     *
     * @return ValidatorInterface|MockObject
     */
    private function getValidatorMock(array $methods = []): ValidatorInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: ValidatorInterface::class,
            mockedMethods: $methods,
        );
    }

    /**
     * @param array<string> $methods
     *
     * @return UploadServiceInterface|MockObject
     */
    private function getUploadServiceMock(array $methods = []): UploadServiceInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: UploadServiceInterface::class,
            mockedMethods: $methods,
        );
    }
}
