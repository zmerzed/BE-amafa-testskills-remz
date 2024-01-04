<?php

namespace Boilerplate\Auth\Http\Resources;

use Boilerplate\Media\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @var \Boilerplate\Auth\Models\User
     */
    public $resource;

    public function toArray($request): array
    {
        return array_merge(
            $this->resource->only(
                [
                    'id',
                    'first_name',
                    'last_name',
                    'description',
                    'birthdate',
                    'gender',
                    'email',
                    'phone_number',
                    'created_at',
                    'updated_at',
                    'blocked_at',
                    'onboarded_at',
                    'primary_username',
                ]
            ),
            [
                // Computed attributes
                'full_name' => $this->resource->full_name,
                'email_verified' => $this->resource->isEmailVerified(),
                'phone_number_verified' => $this->resource->isPhoneNumberVerified(),
                'verified' => $this->resource->isVerified(),
                'avatar_permanent_url' => route(
                    'auth.user.avatar.show',
                    ['id' => $this->id, 'timestamp' => strval(optional($this->updated_at)->timestamp)]
                ),
                'avatar_permanent_thumb_url' => route(
                    'auth.user.avatar.showThumb',
                    ['id' => $this->id, 'timestamp' => strval(optional($this->updated_at)->timestamp)]
                ),
                'mine' => $this->id == optional(auth()->user())->id,
                // Relationship
                'avatar' => MediaResource::make($this->whenLoaded('avatar')),
            ]
        );
    }
}
