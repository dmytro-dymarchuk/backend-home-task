<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\DateTimeFieldsTrait;
use DateTime;
use LogicException;
use PHPUnit\Framework\TestCase;

class DataTimeFieldsTraitTest extends TestCase
{
    public function testPrePersist(): void
    {
        $object = new class() {
            use DateTimeFieldsTrait;
        };

        $object->prePersist();

        self::assertInstanceOf(DateTime::class, $object->getCreatedAt());
        self::assertInstanceOf(DateTime::class, $object->getUpdatedAt());
    }

    public function testPreUpdate(): void
    {
        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('CreatedAt was not set');

        $object = new class() {
            use DateTimeFieldsTrait;
        };

        $object->preUpdate();

        self::assertInstanceOf(DateTime::class, $object->getUpdatedAt());

        $object->getCreatedAt();
    }

    public function testGetUpdatedAtFailed(): void
    {
        $this->expectException(LogicException::class);
        $this->expectDeprecationMessage('UpdatedAt was not set');

        $object = new class() {
            use DateTimeFieldsTrait;
        };

        $object->getUpdatedAt();
    }
}
