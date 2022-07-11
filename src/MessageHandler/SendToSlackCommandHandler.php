<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendToSlackCommand;
use App\Service\TextBuilderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsMessageHandler]
class SendToSlackCommandHandler
{
    public function __construct(
        private ChatterInterface $chatter,
        private TextBuilderInterface $textBuilder,
    ) {
    }

    public function __invoke(SendToSlackCommand $sendEmailCommand): void
    {
        $subject = $this->textBuilder->buildSubject($sendEmailCommand->getTriggerEnum());
        $description = $this->textBuilder->buildDescription($sendEmailCommand->getUpload());

        $chatMessage = (new ChatMessage("$subject. $description"))
            ->transport('slack');

        $this->chatter->send($chatMessage);
    }
}
