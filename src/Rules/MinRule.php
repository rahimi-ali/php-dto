<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class MinRule implements RuleInterface
{
    public function __construct(
        protected readonly int|float $min,
        protected readonly bool $strict = false
    ) {
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        if ($this->strict) {
            return $value > $this->min ?: new ValidationError('min.strict', ['min' => $this->min]);
        } else {
            return $value >= $this->min ?: new ValidationError('min', ['min' => $this->min]);
        }
    }
}
