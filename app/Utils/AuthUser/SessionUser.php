<?php

namespace App\Utils\AuthUser;

use Illuminate\Support\Facades\Auth;


class SessionUser implements AuthUserInterface
{

    public function currentUser()
    {
        return Auth::user();
    }
}