<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class StringRule implements RuleInterface
{
    public function __construct(
        protected readonly bool $strict = false
    ) {
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        return is_string($value)
            || (!$this->strict && !(is_object($value) || is_array($value)))
            ?: new ValidationError('string');
    }
}
