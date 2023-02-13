<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class InRule implements RuleInterface
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(
        protected readonly array $values,
        protected readonly bool $strict = false
    ) {
    }

    public function validate(bool|float|int|string|array|object|null $value): true|ValidationError
    {
        return in_array($value, $this->values, $this->strict)
            ?: new ValidationError('in', ['values' => implode(', ', $this->values)]);
    }
}
