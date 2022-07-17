<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TokenCollection;
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
