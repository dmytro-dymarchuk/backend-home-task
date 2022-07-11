<?php

declare(strict_types=1);

namespace App\Tests\Service\Rule;

use App\Component\Enum\ActionEnum;
use App\Service\Rule\ActionsFactory;
use App\Service\Rule\EmailAction;
use App\Service\Rule\SendToSlackAction;
use PHPUnit\Framework\TestCase;

class ActionsFactoryTest extends TestCase
{
    /**
     * @dataProvider getActionDataProvider
     */
    public function testGetAction(ActionEnum $actionEnum, string $expectedClass): void
    {
        $emailAction = $this->getMockBuilder(EmailAction::class)->disableOriginalConstructor()->getMock();
        $sendToSlackAction = $this->getMockBuilder(SendToSlackAction::class)->disableOriginalConstructor()->getMock();
        self::assertInstanceOf($expectedClass, (new ActionsFactory($emailAction, $sendToSlackAction))->getAction($actionEnum));
    }

    public function getActionDataProvider(): array
    {
        return [
            [
                ActionEnum::SEND_EMAIL,
                EmailAction::class,
            ],
            [
                ActionEnum::SEND_TO_SLACK,
                SendToSlackAction::class,
            ],
        ];
    }
}
