<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\CheckStatusCommand;
use App\MessageHandler\CheckStatusCommandHandler;
use App\Service\Debricked\DebrickedServiceInterface;
use App\Service\Debricked\UploadStatusEnum;
use App\Service\Debricked\UploadStatusResponse;
use App\Service\Rule\RuleServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Throwable;

class CheckStatusCommandHandlerTest extends TestCase
{
    public function __invoke(CheckStatusCommand $message): void
    {
        try {
            $response = $this->debrickedService->checkStatus($message->getCiUploadId());
        } catch (Throwable $e) {
            $this->ruleService->trigger(TriggerEnum::FAIL, $message->getUpload());

            return;
        }

        if (UploadStatusEnum::IN_PROGRESS === $response->getStatusEnum()) {
            $this->messageBus->dispatch(
                new CheckStatusCommand($message->getUpload(), $message->getCiUploadId()),
                [new DelayStamp(60000)]
            );

            return;
        }

        if ($this->allowedVulnerabilitiesCount < $response->getVulnerabilitiesCount()) {
            $this->ruleService->trigger(TriggerEnum::VULNERABILITIES_FOUND, $message->getUpload());
        }
    }

    public function testInvokeSuccessful(): void
    {
        $allowedVulnerabilitiesCount = 10;
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $ciUploadId = 123;
        $message = new CheckStatusCommand($upload, $ciUploadId);
        $uploadStatusResponse = new UploadStatusResponse(UploadStatusEnum::FINISHED, $allowedVulnerabilitiesCount);

        $debrickedServiceMock = $this->getDebrickedServiceMock(['checkStatus']);
        $debrickedServiceMock->expects(self::once())->method('checkStatus')
            ->with($ciUploadId)
            ->willReturn($uploadStatusResponse);

        $ruleServiceMock = $this->getRuleServiceMock();
        $messageBusMock = $this->getMessageBusMock();

        (new CheckStatusCommandHandler(
            $ruleServiceMock,
            $messageBusMock,
            $debrickedServiceMock,
            $allowedVulnerabilitiesCount
        ))->__invoke($message);
    }

    public function testInvokeWithUploadInProgress(): void
    {
        $allowedVulnerabilitiesCount = 10;
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $ciUploadId = 123;
        $message = new CheckStatusCommand($upload, $ciUploadId);
        $uploadStatusResponse = new UploadStatusResponse(UploadStatusEnum::IN_PROGRESS, $allowedVulnerabilitiesCount);

        $debrickedServiceMock = $this->getDebrickedServiceMock(['checkStatus']);
        $debrickedServiceMock->expects(self::once())->method('checkStatus')
            ->with($ciUploadId)
            ->willReturn($uploadStatusResponse);

        $ruleServiceMock = $this->getRuleServiceMock();
        $messageBusMock = $this->getMessageBusMock(['dispatch']);
        $messageBusMock->expects(self::once())->method('dispatch')
            ->with(new CheckStatusCommand($upload, $ciUploadId), [new DelayStamp(60000)])
            ->willReturn(new Envelope(new stdClass()));

        (new CheckStatusCommandHandler(
            $ruleServiceMock,
            $messageBusMock,
            $debrickedServiceMock,
            $allowedVulnerabilitiesCount
        ))->__invoke($message);
    }

    public function testInvokeWithVulnerabilitiesLimitExceeded(): void
    {
        $allowedVulnerabilitiesCount = 10;
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $ciUploadId = 123;
        $message = new CheckStatusCommand($upload, $ciUploadId);
        $uploadStatusResponse = new UploadStatusResponse(UploadStatusEnum::FINISHED, $allowedVulnerabilitiesCount + 1);

        $debrickedServiceMock = $this->getDebrickedServiceMock(['checkStatus']);
        $debrickedServiceMock->expects(self::once())->method('checkStatus')
            ->with($ciUploadId)
            ->willReturn($uploadStatusResponse);

        $ruleServiceMock = $this->getRuleServiceMock(['trigger']);
        $ruleServiceMock->expects(self::once())->method('trigger')
            ->with(TriggerEnum::VULNERABILITIES_FOUND, $upload);
        $messageBusMock = $this->getMessageBusMock();

        (new CheckStatusCommandHandler(
            $ruleServiceMock,
            $messageBusMock,
            $debrickedServiceMock,
            $allowedVulnerabilitiesCount
        ))->__invoke($message);
    }

    public function testInvokeWithError(): void
    {
        $allowedVulnerabilitiesCount = 10;
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $ciUploadId = 123;
        $message = new CheckStatusCommand($upload, $ciUploadId);

        $debrickedServiceMock = $this->getDebrickedServiceMock(['checkStatus']);
        $debrickedServiceMock->expects(self::once())->method('checkStatus')
            ->with($ciUploadId)
            ->willThrowException(new RuntimeException());

        $ruleServiceMock = $this->getRuleServiceMock(['trigger']);
        $ruleServiceMock->expects(self::once())->method('trigger')
            ->with(TriggerEnum::FAIL, $upload);
        $messageBusMock = $this->getMessageBusMock();

        (new CheckStatusCommandHandler(
            $ruleServiceMock,
            $messageBusMock,
            $debrickedServiceMock,
            $allowedVulnerabilitiesCount
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
