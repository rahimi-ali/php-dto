<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use InvalidArgumentException;
use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class MaxLengthRule implements RuleInterface
{
    public function __construct(
        protected readonly int $maxLength,
        protected readonly bool $strict = false
    ) {
        if ($this->maxLength < 0) {
            throw new InvalidArgumentException('The max length must be zero or more.');
        }
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        if (is_string($value)) {
            $length = strlen($value);
        } elseif (is_array($value)) {
            $length = count($value);
        } else {
            throw new InvalidArgumentException('The value must be a string or an array.');
        }

        if ($this->strict) {
            return $length < $this->maxLength ?: new ValidationError('maxLength.strict', ['maxLength' => $this->maxLength]);
        } else {
            return $length <= $this->maxLength ?: new ValidationError('maxLength', ['maxLength' => $this->maxLength]);
        }
    }
}
