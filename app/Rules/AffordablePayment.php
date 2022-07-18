<?php

namespace App\Rules;

use App\Contracts\Payments\PaymentService;
use Illuminate\Contracts\Validation\Rule;

class AffordablePayment implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return app(PaymentService::class)->canAfford($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.affordable_payment');
    }
}
