<?php

declare(strict_types=1);

namespace App\Service\Rule;

use App\Component\Enum\ActionEnum;

class ActionsFactory
{
    public function __construct(
        private EmailAction $emailAction,
        private SendToSlackAction $sendToSlackAction,
    ) {
    }

    public function getAction(ActionEnum $actionEnum): ActionInterface
    {
        return match ($actionEnum) {
            ActionEnum::SEND_EMAIL => $this->emailAction,
            ActionEnum::SEND_TO_SLACK => $this->sendToSlackAction,
        };
    }
}
