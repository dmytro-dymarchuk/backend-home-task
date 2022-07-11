<?php

declare(strict_types=1);

namespace App\Service\Debricked;

enum UploadStatusEnum: int
{
    case IN_PROGRESS = 0;
    case FINISHED = 1;
}
