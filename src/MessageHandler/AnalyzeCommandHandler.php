<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Component\Enum\TriggerEnum;
use App\Message\AnalyzeCommand;
use App\Message\CheckStatusCommand;
use App\Service\Debricked\DebrickedServiceInterface;
use App\Service\Rule\RuleServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Throwable;

#[AsMessageHandler]
class AnalyzeCommandHandler
{
    public function __construct(
        private DebrickedServiceInterface $debrickedService,
        private RuleServiceInterface $ruleService,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(AnalyzeCommand $message): void
    {
        try {
            $ciUploadId = $this->debrickedService->sendToDebricked($message->getUpload());
        } catch (Throwable $e) {
            $this->ruleService->trigger(TriggerEnum::FAIL, $message->getUpload());

            return;
        }

        $this->ruleService->trigger(TriggerEnum::IN_PROGRESS, $message->getUpload());

        $this->messageBus->dispatch(
            new CheckStatusCommand($message->getUpload(), $ciUploadId),
            [new DelayStamp(10000)]
        );
    }
}
