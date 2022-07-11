<?php

declare(strict_types=1);

namespace App\Tests\Service\Rule;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\SendToSlackCommand;
use App\Service\Rule\SendToSlackAction;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SendToSlackActionTest extends TestCase
{
    public function testNotify(): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $messageBus = $this->getMockForAbstractClass(
            originalClassName: MessageBusInterface::class,
            mockedMethods: ['dispatch']
        );
        $messageBus->expects(self::once())->method('dispatch')
            ->with(new SendToSlackCommand(TriggerEnum::IN_PROGRESS, $upload))
            ->willReturn(new Envelope(new stdClass()));

        (new SendToSlackAction($messageBus))->notify(TriggerEnum::IN_PROGRESS, $upload);
    }
}
