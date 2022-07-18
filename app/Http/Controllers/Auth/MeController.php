<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;

class MeController extends Controller
{
    /**
     * Return the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $user = auth()->user();

        return response(UserResource::make($user));
    }
}
