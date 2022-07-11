<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Component\Enum\ActionEnum;
use App\Component\Enum\TriggerEnum;
use App\Entity\UploadInterface;

class RuleService implements RuleServiceInterface
{
    /**
     * @var array<string, array<ActionEnum>> - Key is trigger from {@link TriggerEnum}.
     *                                       Value is array of {@link ActionEnum} which executes for trigger from key.
     */
    private array $config;

    /**
     * @param array<string, string> $config - Key is action from {@link ActionEnum}.
     *                                      Value is set of values {@link TriggerEnum} separated by ",".
     */
    public function __construct(
        private ActionsFactory $actionsFactory,
        array $config
    ) {
        foreach ($config as $action => $triggers) {
            foreach (explode(',', $triggers) as $trigger) {
                $this->config[$trigger][] = ActionEnum::from($action);
            }
        }
    }

    public function trigger(TriggerEnum $triggerEnum, UploadInterface $upload): void
    {
        if (!isset($this->config[$triggerEnum->value])) {
            return;
        }

        foreach ($this->config[$triggerEnum->value] as $action) {
            $this->actionsFactory->getAction($action)->notify($triggerEnum, $upload);
        }
    }
}
