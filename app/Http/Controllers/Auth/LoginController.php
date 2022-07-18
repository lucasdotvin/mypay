<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\NewTokenResource;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * Authenticate the user and return a token.
     *
     * @param  LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LoginRequest $request)
    {
        $token = $request->user()->createToken($request->validated('identity'));

        return response(NewTokenResource::make($token), Response::HTTP_CREATED);
    }
}
