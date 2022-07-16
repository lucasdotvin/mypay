<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreTokenRequest;
use App\Http\Resources\Auth\NewTokenResource;
use App\Http\Resources\Auth\TokenCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResource
     */
    public function index()
    {
        $tokens = request()
            ->user()
            ->tokens()
            ->paginate();

        return TokenCollection::make($tokens);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTokenRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTokenRequest $request)
    {
        $token = $request->user()->createToken($request->validated('identity'));

        return response(NewTokenResource::make($token), Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $token = request()->user()->tokens()->findOrFail($id);

        $token->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
