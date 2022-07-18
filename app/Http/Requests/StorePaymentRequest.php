<?php

namespace App\Http\Requests;

use App\Rules\AffordablePayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount' => ['required', 'integer', 'min:1', new AffordablePayment],
            'message' => ['present', 'string', 'max:255'],
            'payee_id' => ['required', Rule::exists('users', 'id')->whereNot('id', auth()->id())],
        ];
    }
}
