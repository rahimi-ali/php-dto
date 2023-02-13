<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class MaxRule implements RuleInterface
{
    public function __construct(
        protected readonly int|float $max,
        protected readonly bool $strict = false
    ) {
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        if ($this->strict) {
            return $value < $this->max ?: new ValidationError('max.strict', ['max' => $this->max]);
        } else {
            return $value <= $this->max ?: new ValidationError('max', ['max' => $this->max]);
        }
    }
}
