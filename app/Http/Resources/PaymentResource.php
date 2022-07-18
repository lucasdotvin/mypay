<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'message' => $this->message,
            'payee' => new UserReferenceResource($this->payee),
            'payer' => new UserReferenceResource($this->payer),
            'created_at' => $this->created_at->toIsoString(),
        ];
    }
}
