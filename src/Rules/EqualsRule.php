<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\RuleInterface;
use RahimiAli\PhpDto\Support\ValidationError;

class EqualsRule implements RuleInterface
{
    protected const errorKey = 'equals';

    public function __construct(
        protected readonly mixed $value,
        protected readonly bool $strict = false
    ) {
    }

    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     */
    public function validate(bool|float|int|string|array|object|null $value): true|ValidationError
    {
        return $this->isEqual($value) ?: $this->validationError();
    }

    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     */
    protected function isEqual(bool|float|int|string|array|object|null $value): bool
    {
        return match (true) {
            $this->isAssociativeArray($this->value) || is_object($this->value) => $this->validateObject($value), /** @phpstan-ignore-line */
            is_array($this->value) => $this->validateArray($value), /** @phpstan-ignore-line */
            default => $this->validateScalar($value), /** @phpstan-ignore-line */
        };
    }

    private function isAssociativeArray(mixed $array): bool
    {
        return is_array($array) && array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * @param array<mixed> $value
     */
    private function validateArray(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        return $this->strict ? $value == $this->value : count(array_diff($value, $this->value)) === 0; /** @phpstan-ignore-line */
    }

    /**
     * @param array<mixed>|object $value
     */
    private function validateObject(object|array $value): bool
    {
        return $this->value == $value;
    }

    private function validateScalar(bool|float|int|string|null $value): bool
    {
        return $this->strict ? $value === $this->value : $value == $this->value;
    }

    protected function validationError(): ValidationError
    {
        return new ValidationError(
            static::errorKey,
            [
                'value' => match (true) {
                    is_string($this->value) => "'{$this->value}'",
                    is_bool($this->value) => $this->value ? 'true' : 'false',
                    is_null($this->value) => 'null',
                    is_array($this->value) => json_encode($this->value),
                    is_object($this->value) => method_exists($this->value, '__toString')
                        ? $this->value->__toString()
                        : json_encode($this->value),
                    default => $this->value,
                },
            ]
        );
    }
}
