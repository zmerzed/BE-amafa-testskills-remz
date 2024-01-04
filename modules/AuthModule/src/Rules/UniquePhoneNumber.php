<?php

namespace Boilerplate\Auth\Rules;

use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class UniquePhoneNumber implements Rule
{
    use ValidatesPhone;

    /** @var Builder */
    private Builder $query;

    /** @var string */
    private string $column;

    /** @var string */
    private string $attribute;

    public function __construct(mixed $abstract, string $column = "phone_number")
    {
        if ($abstract instanceof EloquentBuilder) {
            $this->query = $abstract->getQuery();
        } elseif ($abstract instanceof Builder) {
            $this->query = $abstract;
        } else {
            $this->query = (new $abstract)->query();
        }

        $this->column = $column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->attribute = $attribute;

        return !$this->query->where($this->column, $this->cleanPhoneNumber($value))->exists();
    }

    public function message(): string
    {
        return __('validation.unique', [':attribute' => $this->attribute]);
    }
}
