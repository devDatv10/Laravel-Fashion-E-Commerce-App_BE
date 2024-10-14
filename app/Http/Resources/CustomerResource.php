<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'customerId' => $this->customerId,
            'userName' => $this->userName,
            'fullName' => $this->fullName,
            'gender' => $this->gender,
            'dateOfBirth' => $this->dateOfBirth,
            'image' => $this->image,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'address' => $this->address,
            'role' => $this->role,
            'isActive' => $this->isActive,
            'lasLogin' => $this->lasLogin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
