<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Message\SendEmailCommand;
use App\MessageHandler\SendEmailCommandHandler;
use App\Service\TextBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendEmailCommandHandlerTest extends TestCase
{
    public function testInvokeSuccess(): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $triggerEnum = TriggerEnum::IN_PROGRESS;
        $email = 'email@email.com';
        $sendToSlackCommand = new SendEmailCommand($triggerEnum, $upload);

        $subject = 'subject';
        $description = 'description';
        $textBuilderMock = $this->getTextBuilderMock(['buildSubject', 'buildDescription']);
        $textBuilderMock->expects(self::once())->method('buildSubject')
            ->with($triggerEnum)
            ->willReturn($subject);
        $textBuilderMock->expects(self::once())->method('buildDescription')
            ->with($upload)
            ->willReturn($description);

        $chatterMock = $this->getMailerMock(['send']);
        $chatterMock->expects(self::once())->method('send')
            ->with(
                (new Email())
                    ->to($email)
                    ->subject($subject)
                    ->text("{$subject}\n{$description}")
                    ->html("<p>$subject</p><p>$description</p>")
            );

        (new SendEmailCommandHandler(
            $chatterMock,
            $textBuilderMock,
            $email,
        ))->__invoke($sendToSlackCommand);
    }

    /**
     * @param array<string> $methods
     *
     * @return MailerInterface|MockObject
     */
    private function getMailerMock(array $methods = []): MailerInterface
    {
        return $this->getMockForAbstractClass(
            originalClassName: MailerInterface::class,
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
