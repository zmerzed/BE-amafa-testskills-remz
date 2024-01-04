<?php

namespace Boilerplate\Auth\Http\Controllers\V1\AccountSettings;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Mail\VerifyChangeEmail;
use Boilerplate\Auth\Middleware\VerificationTokenMiddleware;
use Boilerplate\Auth\Models\ChangeRequest;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Support\CodeGenerator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Boilerplate\Auth\Http\Requests\AccountSettings\ChangeEmailRequest;
use Boilerplate\Auth\Http\Requests\AccountSettings\ChangeVerificationRequest;

class ChangeEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(VerificationTokenMiddleware::class);
    }

    /**
     * When users send a request to change his email, a verification code
     * will be sent and will be used to verify before he/she can update
     * his/her old email.
     *
     * @throws \Exception
     */
    public function change(ChangeEmailRequest $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        $newEmail = $request->input('email');

        if ($user->isEmailPrimary()) {
            rescue(function () use ($user, $newEmail) {
                $code = CodeGenerator::make();

                $user->changeRequestFor('email', $newEmail, $code);

                Mail::to($newEmail)
                    ->send(new VerifyChangeEmail($user, $code));
            });
        } else {
            $user->email = $newEmail;
            $user->save();
        }

        return UserResource::make($user->fresh('avatar'));
    }

    /**
     * Once the token was verified and valid, the changes will be applied.
     */
    public function verify(ChangeVerificationRequest $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        /** @var ChangeRequest $changeRequest */
        $changeRequest = $user->getChangeRequestFor('email');

        if (is_null($changeRequest)) {
            abort(Response::HTTP_NOT_FOUND, 'No request for change email.');
        }

        if (!$changeRequest->isTokenValid($request->input('token'))) {
            abort(Response::HTTP_BAD_REQUEST, 'The verification code was invalid.');
        }

        $user->applyChangeRequest($changeRequest);
        $user->verifyEmailNow();

        return UserResource::make($user->refresh());
    }
}
