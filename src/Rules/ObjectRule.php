<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class ObjectRule implements RuleInterface
{
    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        return is_object($value)
            || (
                is_array($value) &&
                (
                    count(array_filter(array_keys($value), 'is_string')) === count($value)
                    || array_keys($value) !== range(0, count($value) - 1)
                )
            )
            ?: new ValidationError('object');
    }
}
