<?php

declare(strict_types=1);

namespace App\Component;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListNormalizer implements ConstraintViolationListNormalizerInterface
{
    /**
     * @return array<mixed>
     */
    public function normalize(ConstraintViolationListInterface $constraintViolationList): array
    {
        $errors = [];

        foreach ($constraintViolationList as $violation) {
            $errors[$violation->getPropertyPath()][$violation->getCode()] = $violation->getMessage();
        }

        return $errors;
    }
}
