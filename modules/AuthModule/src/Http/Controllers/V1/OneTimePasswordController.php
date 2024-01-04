<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\GenerateOTPRequest;
use Boilerplate\Auth\Support\OneTimePassword\InteractsWithOneTimePassword;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Http\JsonResponse;

class OneTimePasswordController extends Controller
{
    use InteractsWithOneTimePassword;
    use ValidatesPhone;

    /**
     * Handle the incoming request.
     */
    public function generate(GenerateOTPRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $this->sendOneTimePassword($this->uncleanPhoneNumber($payload['phone_number']));

        return $this->respondWithEmptyData();
    }
}
