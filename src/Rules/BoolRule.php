<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class BoolRule implements RuleInterface
{
    public function __construct(
        protected readonly bool $strict = false
    ) {
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        return is_bool($value)
            || (!$this->strict && filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null)
            ?: new ValidationError('bool');
    }
}
