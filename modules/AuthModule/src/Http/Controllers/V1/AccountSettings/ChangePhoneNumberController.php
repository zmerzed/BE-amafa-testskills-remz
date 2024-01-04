<?php

namespace Boilerplate\Auth\Http\Controllers\V1\AccountSettings;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\ChangeRequest;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Support\CodeGenerator;
use Boilerplate\Sms\Facades\Sms;
use Boilerplate\Sms\SmsMessage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Boilerplate\Auth\Http\Requests\AccountSettings\ChangePhoneNumberRequest;
use Boilerplate\Auth\Http\Requests\AccountSettings\ChangeVerificationRequest;

class ChangePhoneNumberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verification-token');
    }

    /**
     * When users send a request to change his email, a verification code
     * will be sent and will be used to verify before he/she can update
     * his/her old email.
     */
    public function change(ChangePhoneNumberRequest $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        $newPhoneNumber = $request->input('phone_number');

        if ($user->isPhonePrimary()) {
            rescue(function () use ($user, $newPhoneNumber) {
                $code = CodeGenerator::make();

                $user->changeRequestFor('phone_number', $newPhoneNumber, $code);
                // Send verification code
                $content = Lang::get('Your phone number verification code is :code', ['code' => $code]);

                Sms::send($newPhoneNumber, new SmsMessage($content));
            });
        } else {
            $user->phone_number = $newPhoneNumber;
            $user->save();
        }

        return UserResource::make($user->refresh('avatar'));
    }

    /**
     * Once the token was verified and valid, the changes will be applied.
     */
    public function verify(ChangeVerificationRequest $request): UserResource
    {
        /** @var User $request */
        $user = $request->user();

        /** @var ChangeRequest $changeRequest */
        $changeRequest = $user->getChangeRequestFor('phone_number');

        if (is_null($changeRequest)) {
            abort(Response::HTTP_NOT_FOUND, 'No request for change phone number.');
        }

        if (!$changeRequest->isTokenValid($request->input('token'))) {
            abort(Response::HTTP_BAD_REQUEST, 'The verification code was invalid.');
        }

        $user->applyChangeRequest($changeRequest);
        $user->verifyPhoneNumberNow();

        return UserResource::make($user->refresh('avatar'));
    }
}
