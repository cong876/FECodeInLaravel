<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests;
use Auth;

class LogoutAllUserController extends Controller
{
    public function logoutAll()
    {
        $users = User::all();
        foreach($users as $user) {
            Auth::login($user);
            Auth::logout();
        }
    }


}

