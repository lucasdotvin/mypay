<?php

namespace App\Exceptions\Permissions;

use Exception;

class CantPerformAction extends Exception
{
    public function __construct()
    {
        $this->message = trans('exceptions.cant_perform_action');
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(['message' => $this->message], 403);
    }
}
