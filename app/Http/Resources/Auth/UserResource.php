<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'document' => $this->document,
            'email' => $this->email,
            'balance' => $this->balance,
            'role' => RoleResource::make($this->role),
            'created_at' => $this->created_at->toIsoString(),
        ];
    }
}
