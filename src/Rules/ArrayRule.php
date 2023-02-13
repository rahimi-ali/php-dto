<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class ArrayRule implements RuleInterface
{
    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        return (is_array($value) && array_keys($value) === range(0, count($value) - 1))
            ?: new ValidationError('array');
    }
}
