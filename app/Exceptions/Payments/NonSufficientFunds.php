<?php

namespace App\Exceptions\Payments;

use Exception;

class NonSufficientFunds extends Exception
{
    public function __construct() {
        $this->message = trans('exceptions.non_sufficient_funds');
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(['message' => $this->message], 400);
    }
}
