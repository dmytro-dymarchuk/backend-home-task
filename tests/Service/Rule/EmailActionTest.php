<?php

declare(strict_types=1);

namespace App\Tests\Service\Rule;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\SendEmailCommand;
use App\Service\Rule\EmailAction;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailActionTest extends TestCase
{
    public function testNotify(): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $messageBus = $this->getMockForAbstractClass(
            originalClassName: MessageBusInterface::class,
            mockedMethods: ['dispatch']
        );
        $messageBus->expects(self::once())->method('dispatch')
            ->with(new SendEmailCommand(TriggerEnum::IN_PROGRESS, $upload))
            ->willReturn(new Envelope(new stdClass()));

        (new EmailAction($messageBus))->notify(TriggerEnum::IN_PROGRESS, $upload);
    }
}
