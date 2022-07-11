<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendEmailCommand;
use App\Service\TextBuilderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class SendEmailCommandHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private TextBuilderInterface $textBuilder,
        private string $email,
    ) {
    }

    public function __invoke(SendEmailCommand $sendEmailCommand): void
    {
        $subject = $this->textBuilder->buildSubject($sendEmailCommand->getTriggerEnum());
        $description = $this->textBuilder->buildDescription($sendEmailCommand->getUpload());
        $email = (new Email())
            ->to($this->email)
            ->subject($subject)
            ->text("{$subject}\n{$description}")
            ->html("<p>$subject</p><p>$description</p>");

        $this->mailer->send($email);
    }
}
