<?php

namespace App\Http\Controllers;

use App\Contracts\UserRepository;
use App\Http\Resources\UserCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResource
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->getUsersExcept(auth()->id(), 'first_name');

        return UserCollection::make($users);
    }
}
