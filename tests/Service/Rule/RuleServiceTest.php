<?php

declare(strict_types=1);

namespace App\Tests\Service\Rule;

use App\Component\Enum\ActionEnum;
use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;
use App\Service\Rule\ActionsFactory;
use App\Service\Rule\RuleService;
use PHPUnit\Framework\TestCase;

class RuleServiceTest extends TestCase
{
    /**
     * @dataProvider triggerDataProvider
     */
    public function testTrigger(array $config, TriggerEnum $triggerEnum, array $expectedActions): void
    {
        $upload = $this->getMockForAbstractClass(UploadInterface::class);
        $actionsFactory = $this->getMockBuilder(ActionsFactory::class)->disableOriginalConstructor()->getMock();

        if ([] === $expectedActions) {
            $actionsFactory->expects(self::never())->method('getAction');
        } else {
            $actionsFactory->expects(self::exactly(\count($expectedActions)))->method('getAction')
                ->withConsecutive(...array_map(fn (ActionEnum $actionEnum): array => [$actionEnum], $expectedActions));
        }

        (new RuleService($actionsFactory, $config))->trigger($triggerEnum, $upload);
    }

    public function triggerDataProvider(): array
    {
        return [
            [
                [
                    'send_email' => 'vulnerabilities_found,in_progress',
                    'send_to_slack' => 'in_progress,fail',
                ],
                TriggerEnum::VULNERABILITIES_FOUND,
                [ActionEnum::SEND_EMAIL],
            ],
            [
                [
                    'send_email' => 'vulnerabilities_found,in_progress',
                    'send_to_slack' => 'in_progress,fail',
                ],
                TriggerEnum::IN_PROGRESS,
                [ActionEnum::SEND_EMAIL, ActionEnum::SEND_TO_SLACK],
            ],
            [
                [
                    'send_email' => 'vulnerabilities_found,in_progress',
                    'send_to_slack' => 'in_progress,fail',
                ],
                TriggerEnum::FAIL,
                [ActionEnum::SEND_TO_SLACK],
            ],
            [
                [
                    'send_email' => 'vulnerabilities_found',
                    'send_to_slack' => 'fail',
                ],
                TriggerEnum::IN_PROGRESS,
                [],
            ],
            [
                [
                    'send_email' => 'vulnerabilities_found',
                ],
                TriggerEnum::IN_PROGRESS,
                [],
            ],
            [
                [
                    'send_to_slack' => 'fail',
                ],
                TriggerEnum::IN_PROGRESS,
                [],
            ],
        ];
    }
}
