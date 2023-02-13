<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use RahimiAli\PhpDto\Rules\IntRule;
use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\ValidationException;

class IntType implements TypeInterface
{
    public function __construct(
        protected readonly bool $strict = false
    ) {
    }

    public function cast(bool|float|int|string|array|object|null $value): int
    {
        if (($error = (new IntRule($this->strict))->validate($value)) === true) {
            return (int) $value; /** @phpstan-ignore-line */
        }

        throw new ValidationException(['' => [$error]]);
    }
}
