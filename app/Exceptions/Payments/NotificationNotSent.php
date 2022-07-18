<?php

namespace App\Exceptions\Payments;

use Exception;

class NotificationNotSent extends Exception
{
    public function __construct()
    {
        $this->message = trans('exceptions.notification_not_sent');
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(['message' => $this->message], 500);
    }
}
