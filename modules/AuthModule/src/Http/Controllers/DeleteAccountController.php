<?php

namespace Boilerplate\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Boilerplate\Auth\Models\User;

class DeleteAccountController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        $user->delete();

        return "Your account has successfully deleted.";
    }
}
