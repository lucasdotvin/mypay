<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;

class SignUpController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param  SignUpRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SignUpRequest $request, UserService $userService)
    {
        $user = $userService->create(
            $request->first_name,
            $request->last_name,
            $request->email,
            $request->document,
            $request->password,
            $request->role
        );

        event(new Registered($user));

        return response(new UserResource($user), Response::HTTP_CREATED);
    }
}
