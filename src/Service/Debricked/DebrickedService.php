<?php

declare(strict_types=1);

namespace App\Service\Debricked;

use App\Entity\LockFileInterface;
use App\Entity\UploadInterface;
use App\Service\FileSystemServiceInterface;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

class DebrickedService implements DebrickedServiceInterface
{
    private const UPLOAD_FILE_URI = '/api/%s/open/uploads/dependencies/files';
    private const FINALIZE_UPLOAD_URI = '/api/%s/open/finishes/dependencies/files/uploads';
    private const GET_STATUS_URI = '/api/%s/open/ci/upload/status';

    private const API_HOST = 'https://debricked.com';
    private const API_VERSION = '1.0';

    public function __construct(
        private HttpClientInterface $httpClient,
        private FileSystemServiceInterface $fileSystemService,
        private string $token,
        private string $repository,
    ) {
    }

    public function sendToDebricked(UploadInterface $upload): int
    {
        $ciUploadId = null;
        foreach ($upload->getFiles() as $file) {
            $ciUploadId = $this->sendFile($upload->getIdStrict(), $file, $ciUploadId);
        }

        Assert::integer($ciUploadId);

        $this->finalize($ciUploadId);

        return $ciUploadId;
    }

    public function checkStatus(int $ciUploadId): UploadStatusResponse
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            $this->prepareUrl(self::GET_STATUS_URI),
            [
                'headers' => $this->getDefaultHeaders(),
                'query' => [
                    'ciUploadId' => $ciUploadId,
                ],
            ],
        );

        $content = $response->getContent(false);

        if (Response::HTTP_OK === $response->getStatusCode()) {
            return new UploadStatusResponse(
                UploadStatusEnum::FINISHED,
                Json::decode($content, Json::FORCE_ARRAY)['vulnerabilitiesFound'],
            );
        }

        return new UploadStatusResponse(UploadStatusEnum::IN_PROGRESS);
    }

    private function sendFile(int $uploadId, LockFileInterface $lockFile, ?int $ciUploadId): int
    {
        $formFields = [
            'fileData' => DataPart::fromPath($this->fileSystemService->getFilePath($lockFile)),
            'repositoryName' => $this->repository,
            'commitName' => (string) $uploadId,
        ];

        if (null !== $ciUploadId) {
            $formFields['ciUploadId'] = (string) $ciUploadId;
        }

        $formDataPart = new FormDataPart($formFields);

        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->prepareUrl(self::UPLOAD_FILE_URI),
                [
                    'headers' => array_merge(
                        $this->getDefaultHeaders(),
                        $formDataPart->getPreparedHeaders()->toArray(),
                    ),
                    'body' => $formDataPart->bodyToIterable(),
                ],
        );

        $content = $response->getContent();

        return Json::decode($content, Json::FORCE_ARRAY)['ciUploadId'];
    }

    private function finalize(int $ciUploadId): void
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            $this->prepareUrl(self::FINALIZE_UPLOAD_URI),
            [
                'headers' => $this->getDefaultHeaders(),
                'body' => [
                    'ciUploadId' => $ciUploadId,
                ],
            ],
        );

        $response->getContent();
    }

    private function prepareUrl(string $uriTemplate): string
    {
        return self::API_HOST.sprintf($uriTemplate, self::API_VERSION);
    }

    /**
     * @return array<string, string>
     */
    private function getDefaultHeaders(): array
    {
        return [
            'Authorization' => "Bearer $this->token",
        ];
    }
}
