<?php

declare(strict_types=1);

namespace App\Tests\Component;

use App\Component\ConstraintViolationListNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationListNormalizerTest extends TestCase
{
    /**
     * @dataProvider normalizeDataProvider
     */
    public function testNormalize(
        array $violationData,
        array $expectedResult
    ): void {
        $constraintViolationList = new ConstraintViolationList();
        foreach ($violationData as $violation) {
            $constraintViolationList->add(
                new ConstraintViolation(
                    $violation['message'],
                    null,
                    [],
                    null,
                    $violation['propertyPath'],
                    null,
                    null,
                    $violation['code'],
                )
            );
        }

        self::assertSame($expectedResult, (new ConstraintViolationListNormalizer())->normalize($constraintViolationList));
    }

    public function normalizeDataProvider(): array
    {
        return [
            [
                // Violations data
                [
                    [
                        'message' => 'message1',
                        'code' => 'code1',
                        'propertyPath' => 'property1',
                    ],
                    [
                        'message' => 'message2',
                        'code' => 'code2',
                        'propertyPath' => 'property2',
                    ],
                ],
                // Expected result
                [
                    'property1' => [
                        'code1' => 'message1',
                    ],
                    'property2' => [
                        'code2' => 'message2',
                    ],
                ],
            ],
            [
                // Violations data
                [
                    [
                        'message' => 'message1',
                        'code' => 'code1',
                        'propertyPath' => 'property1',
                    ],
                    [
                        'message' => 'message2',
                        'code' => 'code2',
                        'propertyPath' => 'property1',
                    ],
                ],
                // Expected result
                [
                    'property1' => [
                        'code1' => 'message1',
                        'code2' => 'message2',
                    ],
                ],
            ],
        ];
    }
}
