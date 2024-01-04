<?php

namespace Boilerplate\Auth\Models\Traits;

use Boilerplate\Auth\Models\ChangeRequest;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait InteractsWithChangeRequest
{
    public function changedRequests(): MorphMany
    {
        return $this->morphMany(ChangeRequest::class, 'changeable');
    }

    /**
     * @throws \Exception
     */
    public function changeRequestFor(string $fieldName, mixed $newValue, ?string $token = null): ChangeRequest
    {
        if (!array_key_exists($fieldName, $this->attributes)) {
            throw new Exception("The $fieldName does not exists!");
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->changedRequests()
            ->updateOrCreate(
                ['field_name' => $fieldName,],
                [
                    'from' => $this->attributes[$fieldName],
                    'to' => $newValue,
                    'token' => !is_null($token) ? Hash::make($token) : null,
                ]
            );
    }

    public function getChangeRequestFor(string $fieldName): ?ChangeRequest
    {
        return $this->changedRequests()
            ->whereFieldName($fieldName)
            ->first();
    }

    public function applyChangeRequest(ChangeRequest $changeRequest): void
    {
        DB::transaction(function () use ($changeRequest) {
            $this->setAttribute($changeRequest->field_name, $changeRequest->to);
            $this->save();

            $this->changedRequests()
                ->whereId($changeRequest->id)
                ->delete();
        });
    }
}
