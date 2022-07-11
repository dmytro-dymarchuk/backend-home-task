<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\AnalyzeCommand;
use App\Message\CheckStatusCommand;
use App\MessageHandler\AnalyzeCommandHandler;
use App\Service\Debricked\DebrickedServiceInterface;
use App\Service\Rule\RuleServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class AnalyzeCommandHandlerTest extends TestCase
{
    public function testInvokeSuccessful(): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $message = new AnalyzeCommand($upload);
        $ciUploadId = 123;

        $debrickedServiceMock = $this->getDebrickedServiceMock(['sendToDebricked']);
        $debrickedServiceMock->expects(self::once())->method('sendToDebricked')
            ->with($upload)
            ->willReturn($ciUploadId);

        $ruleServiceMock = $this->getRuleServiceMock(['trigger']);
        $ruleServiceMock->expects(self::once())->method('trigger')
            ->with(TriggerEnum::IN_PROGRESS, $upload);

        $messageBusMock = $this->getMessageBusMock(['dispatch']);
        $messageBusMock->expects(self::once())->method('dispatch')
            ->with(new CheckStatusCommand($upload, $ciUploadId), [new DelayStamp(10000)])
            ->willReturn(new Envelope(new stdClass()));

        (new AnalyzeCommandHandler(
            $debrickedServiceMock,
            $ruleServiceMock,
            $messageBusMock,
        ))->__invoke($message);
    }

    public function testInvokeWithFail(): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $message = new AnalyzeCommand($upload);

        $debrickedServiceMock = $this->getDebrickedServiceMock(['sendToDebricked']);
        $debrickedServiceMock->expects(self::once())->method('sendToDebricked')
            ->with($upload)
            ->willThrowException(new RuntimeException());

        $ruleServiceMock = $this->getRuleServiceMock(['trigger']);
        $ruleServiceMock->expects(self::once())->method('trigger')
            ->with(TriggerEnum::FAIL, $upload);

        $messageBusMock = $this->getMessageBusMock();

        (new AnalyzeCommandHandler(
            $debrickedServiceMock,
            $ruleServiceMock,
            $messageBusMock,
        ))->__invoke($message);
    }

    /**
     * @param array<string> $methods
     *
     * @return DebrickedServiceInterface|MockObject
     */
    private function getDebrickedServiceMock(array $methods = []): DebrickedServiceInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: DebrickedServiceInterface::class,
            mockedMethods: $methods,
        );
    }

    /**
     * @param array<string> $methods
     *
     * @return RuleServiceInterface|MockObject
     */
    private function getRuleServiceMock(array $methods = []): RuleServiceInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: RuleServiceInterface::class,
            mockedMethods: $methods,
        );
    }

    /**
     * @param array<string> $methods
     *
     * @return MessageBusInterface|MockObject
     */
    private function getMessageBusMock(array $methods = []): MessageBusInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: MessageBusInterface::class,
            mockedMethods: $methods,
        );
    }
}
