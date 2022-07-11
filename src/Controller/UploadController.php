<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\ConstraintViolationListNormalizerInterface;
use App\Dto\InputUploadDto;
use App\Service\UploadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadController extends AbstractController
{
    public function __construct(
        private ConstraintViolationListNormalizerInterface $constraintViolationListNormalizer,
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
        private UploadServiceInterface $uploadService,
    ) {
    }

    /**
     * CURL example:.
     *
     * curl --location --request POST 'http://localhost:8888/api/uploads' \
     *  --form 'files[]=@"PATH_TO_YOUR_FILE_1"' \
     *  --form 'files[]=@"PATH_TO_YOUR_FILE_2"'
     *
     * Responses:
     *
     * 204 - Files were uploaded successfully.
     * 400 - Bad request. E.g:
     *
     * {
     *     "files[0]": {
     *         "5d743385-9775-4aa5-8ff5-495fb1e60137": "An empty file is not allowed."
     *     }
     * }
     */
    #[Route(path: '/api/uploads', name: 'create-upload-action', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        /** @var InputUploadDto $uploadDto */
        $uploadDto = $this->denormalizer->denormalize($request->files->all(), InputUploadDto::class);

        $constraintViolationList = $this->validator->validate($uploadDto);

        if ($constraintViolationList->count() > 0) {
            return new JsonResponse(
                $this->constraintViolationListNormalizer->normalize($constraintViolationList),
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->uploadService->saveFromUploads($uploadDto->getValidFiles());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
