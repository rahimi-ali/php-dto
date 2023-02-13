<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use RahimiAli\PhpDto\Dto;
use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\Rules\ObjectRule;
use RahimiAli\PhpDto\ValidationException;

/**
 * @template T of Dto
 */
class EmbeddedType implements TypeInterface
{
    /**
     * @param class-string<T> $class
     */
    public function __construct(
        protected readonly string $class
    ) {
    }

    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     * @return T
     * @throws ValidationException
     */
    public function cast(float|object|int|bool|array|string|null $value): object
    {
        if ($value instanceof $this->class) {
            return $value;
        }

        if (($validationError = (new ObjectRule())->validate($value)) !== true) {
            throw new ValidationException(['' => [$validationError]]);
        }

        return new $this->class($value);
    }
}
