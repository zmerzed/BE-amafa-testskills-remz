<?php

use Illuminate\Support\Facades\Route;
use Boilerplate\Auth\Http\Controllers\DeleteAccountController;


Route::get('/account-deletion', [DeleteAccountController::class, 'index'])
    ->middleware('signed')
    ->name('account-deletion');
