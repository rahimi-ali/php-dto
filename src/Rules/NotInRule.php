<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class NotInRule implements RuleInterface
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(
        protected readonly array $values,
        protected readonly bool $strict = false
    ) {
    }

    public function validate(float|object|int|bool|array|string|null $value): true|ValidationError
    {
        return !in_array($value, $this->values, $this->strict)
            ?: new ValidationError('notIn', ['values' => implode(', ', $this->values)]);
    }
}
