<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class InputUploadDto
{
    /**
     * @var array<mixed>
     */
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[Assert\Count(min: 1, max: 10)]
    #[Assert\All(
        new Assert\File(
            maxSize: '5M',
            mimeTypes: ['application/json', 'text/plain'],
            mimeTypesMessage: 'Please upload a valid lock file.',
        )
    )]
    private mixed $files;

    public function __construct(mixed $files)
    {
        $this->files = $files;
    }

    /**
     * @return array<UploadedFile>
     */
    public function getValidFiles(): array
    {
        \Webmozart\Assert\Assert::isArray($this->files);

        return $this->files;
    }
}
