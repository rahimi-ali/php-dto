<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\Rules\StringRule;
use RahimiAli\PhpDto\ValidationException;

class StringType implements TypeInterface
{
    public function __construct(
        protected readonly bool $strict = false
    ) {
    }

    public function cast(float|object|int|bool|array|string|null $value): bool|float|int|string|array|object|null
    {
        if (($error = (new StringRule($this->strict))->validate($value)) === true) {
            return (string) $value; /** @phpstan-ignore-line */
        }

        throw new ValidationException(['' => [$error]]);
    }
}
