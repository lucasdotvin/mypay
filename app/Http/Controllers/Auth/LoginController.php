<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\NewTokenResource;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $token = $request->user()->createToken($request->validated('identity'));

        return response(NewTokenResource::make($token), Response::HTTP_CREATED);
    }
}
