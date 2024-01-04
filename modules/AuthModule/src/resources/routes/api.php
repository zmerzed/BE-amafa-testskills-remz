<?php

use Boilerplate\Auth\Enums\Role;
use Illuminate\Support\Facades\Route;
use Boilerplate\Auth\Http\Controllers\V1\AuthController;
use Boilerplate\Auth\Http\Controllers\V1\UserController;
use Boilerplate\Auth\Http\Controllers\V1\CheckController;
use Boilerplate\Auth\Http\Controllers\V1\ProfileController;
use Boilerplate\Auth\Http\Controllers\V1\RegisterController;
use Boilerplate\Auth\Http\Controllers\V1\OnBoardingController;
use Boilerplate\Auth\Http\Controllers\V1\UserAvatarController;
use Boilerplate\Auth\Http\Controllers\V1\VerificationController;
use Boilerplate\Auth\Http\Controllers\V1\ProfileAvatarController;
use Boilerplate\Auth\Http\Controllers\V1\ResetPasswordController;
use Boilerplate\Auth\Http\Controllers\V1\ForgotPasswordController;
use Boilerplate\Auth\Http\Controllers\V1\OneTimePasswordController;
use Boilerplate\Auth\Http\Controllers\V1\AccountDeletionController;
use Boilerplate\Auth\Http\Controllers\V1\Admin\UserAccountAccessController;
use Boilerplate\Auth\Http\Controllers\V1\AccountSettings\ChangeEmailController;
use Boilerplate\Auth\Http\Controllers\V1\AccountSettings\DeleteAccountController;
use Boilerplate\Auth\Http\Controllers\V1\AccountSettings\ChangePasswordController;
use Boilerplate\Auth\Http\Controllers\V1\AccountSettings\ChangePhoneNumberController;
use Boilerplate\Auth\Http\Controllers\V1\AccountSettings\VerificationTokenController;


Route::apiResource('users', UserController::class)->except('store');

Route::get('users/{id}/avatar', [UserAvatarController::class, 'show'])->name('user.avatar.show');
Route::get('users/{id}/avatar/thumb', [UserAvatarController::class, 'showThumb'])->name('user.avatar.showThumb');

Route::post('users/{user}/avatar', [UserAvatarController::class, 'store'])->name('user.avatar.store');
Route::delete('users/{user}/avatar', [UserAvatarController::class, 'destroy'])->name('user.avatar.destroy');
Route::get('users/{id}/avatar', [UserAvatarController::class, 'show'])->name('user.avatar.show');
Route::get('users/{id}/avatar/thumb', [UserAvatarController::class, 'showThumb'])->name('user.avatar.showThumb');

Route::prefix('auth')
    ->group(
        function () {
            Route::post('check-email', [CheckController::class, 'checkEmail'])->name('checkEmail');
            Route::post('check-username', [CheckController::class, 'checkUsername'])->name('checkUsername');

            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');

            Route::get('/profile', [ProfileController::class, 'index']);
            Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);
            Route::post('/profile/avatar', [ProfileAvatarController::class, 'store']);

            Route::post('register', RegisterController::class)->name('register');
            Route::post('forgot-password', ForgotPasswordController::class)->name('forgotPassword');
            Route::post('reset-password', ResetPasswordController::class)->name('resetPassword');
            Route::post('reset-password/check', [ResetPasswordController::class, 'checkToken'])->name(
                'resetPassword.check'
            );
            Route::post('reset-password/get-verified-account', [ResetPasswordController::class, 'getVerifiedAccount'])
                ->name('resetPassword.get-verified-account');
            Route::post('verification/verify', [VerificationController::class, 'verify'])->name('verification.verify');
            Route::post('verification/resend', [VerificationController::class, 'resend'])->name('verification.resend');

            Route::post('otp/generate', [OneTimePasswordController::class, 'generate']);

            Route::post('onboarding/email', [OnBoardingController::class, 'email']);
            Route::post('onboarding/complete', [OnBoardingController::class, 'complete']);


            Route::post('change/email', [ChangeEmailController::class, 'change']);
            Route::post('change/email/verify', [ChangeEmailController::class, 'verify']);

            Route::post('change/phone-number', [ChangePhoneNumberController::class, 'change']);
            Route::post('change/phone-number/verify', [ChangePhoneNumberController::class, 'verify']);

            Route::post('change/password', ChangePasswordController::class);

            Route::post('account/verification-token', VerificationTokenController::class);

            Route::delete('account', DeleteAccountController::class);
            Route::post('account-deletion', [AccountDeletionController::class, 'store']);
            Route::post('account-deletion/confirm', [AccountDeletionController::class, 'confirm']);
        }
    );

Route::group(['prefix' => 'admin', 'middleware' => ['user.role:' . Role::ADMIN, 'auth']], function () {
    Route::post('users/{user}/disable', [UserAccountAccessController::class, 'blockUserAccess']);
    Route::post('users/{user}/enable', [UserAccountAccessController::class, 'grantUserAccess']);
});
