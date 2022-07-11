<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Component\Enum\TriggerEnum;
use App\Message\CheckStatusCommand;
use App\Service\Debricked\DebrickedServiceInterface;
use App\Service\Debricked\UploadStatusEnum;
use App\Service\Rule\RuleServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Throwable;

#[AsMessageHandler]
class CheckStatusCommandHandler
{
    public function __construct(
        private RuleServiceInterface $ruleService,
        private MessageBusInterface $messageBus,
        private DebrickedServiceInterface $debrickedService,
        private int $allowedVulnerabilitiesCount,
    ) {
    }

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
}
