<?php

namespace Boilerplate\Auth\Http\Controllers;

use App\Support\Response\ReturnsResponse;
use Boilerplate\Auth\Enums\ErrorCodes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

/**
 * @method JsonResponse respondWithToken(string $token, mixed $user, int $statusCode = 200)
 * @method JsonResponse respondWithEmptyData(int $statusCode = 200)
 */
abstract class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    use ReturnsResponse {
        respondWithError as respondWithErrorTrait;
    }

    protected function respondWithError(
        string $errorCode,
        int $statusCode = 400,
        ?string $message = null,
        array $metadata = []
    ): JsonResponse {
        return $this->respondWithErrorTrait(
            $errorCode,
            $statusCode,
            $message ?: ErrorCodes::getDescription($errorCode),
            $metadata
        );
    }
}
