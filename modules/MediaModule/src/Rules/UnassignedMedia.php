<?php

namespace Boilerplate\Media\Rules;

use Boilerplate\Media\Models\Media;
use Illuminate\Contracts\Validation\Rule;

class UnassignedMedia implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Media::OnlyUnassigned()->whereKey($value)->exists();
    }

    public function message(): string
    {
        return 'The :attribute is invalid.';
    }
}
