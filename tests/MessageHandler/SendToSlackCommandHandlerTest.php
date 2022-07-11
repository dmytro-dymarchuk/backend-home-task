<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\SendToSlackCommand;
use App\MessageHandler\SendToSlackCommandHandler;
use App\Service\TextBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class SendToSlackCommandHandlerTest extends TestCase
{
    public function testInvokeSuccess(): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $triggerEnum = TriggerEnum::IN_PROGRESS;
        $sendToSlackCommand = new SendToSlackCommand($triggerEnum, $upload);

        $subject = 'subject';
        $description = 'description';
        $textBuilderMock = $this->getTextBuilderMock(['buildSubject', 'buildDescription']);
        $textBuilderMock->expects(self::once())->method('buildSubject')
            ->with($triggerEnum)
            ->willReturn($subject);
        $textBuilderMock->expects(self::once())->method('buildDescription')
            ->with($upload)
            ->willReturn($description);

        $chatterMock = $this->getChatterMock(['send']);
        $chatterMock->expects(self::once())->method('send')
            ->with((new ChatMessage('subject. description'))->transport('slack'));

        (new SendToSlackCommandHandler(
            $chatterMock,
            $textBuilderMock,
        ))->__invoke($sendToSlackCommand);
    }

    /**
     * @param array<string> $methods
     *
     * @return ChatterInterface|MockObject
     */
    private function getChatterMock(array $methods = []): ChatterInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: ChatterInterface::class,
            mockedMethods: $methods,
        );
    }

    /**
     * @param array<string> $methods
     *
     * @return TextBuilderInterface|MockObject
     */
    private function getTextBuilderMock(array $methods = []): TextBuilderInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: TextBuilderInterface::class,
            mockedMethods: $methods,
        );
    }
}
