<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\SendToSlackCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class SendToSlackAction implements ActionInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function notify(TriggerEnum $triggerEnum, UploadInterface $upload): void
    {
        $this->messageBus->dispatch(new SendToSlackCommand($triggerEnum, $upload));
    }
}
