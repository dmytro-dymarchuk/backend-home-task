<?php

declare(strict_types=1);

namespace App\Component;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ConstraintViolationListNormalizerInterface
{
    /**
     * Converts ConstraintViolationListInterface to normalized array.
     *
     * @return array<mixed>
     */
    public function normalize(ConstraintViolationListInterface $constraintViolationList): array;
}
