<?php

namespace Boilerplate\Auth\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PasswordResetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'email' => $this->email,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}
