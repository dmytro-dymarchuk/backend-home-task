<?php

declare(strict_types=1);

namespace App\Service\Debricked;

use App\Entity\UploadInterface;

interface DebrickedServiceInterface
{
    /**
     * @return int - Return ciUploadId in Debricked
     */
    public function sendToDebricked(UploadInterface $upload): int;

    /**
     * @param int $ciUploadId - ciUploadId params received from Debricked API
     */
    public function checkStatus(int $ciUploadId): UploadStatusResponse;
}
