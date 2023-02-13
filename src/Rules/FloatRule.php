<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class FloatRule implements RuleInterface
{
    public function __construct(
        protected readonly bool $strict = false
    ) {
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        return is_float($value)
            || (!$this->strict && filter_var($value, FILTER_VALIDATE_FLOAT) !== false)
            ?: new ValidationError('float');
    }
}
