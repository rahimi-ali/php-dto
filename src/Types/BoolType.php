<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\Rules\BoolRule;
use RahimiAli\PhpDto\ValidationException;

class BoolType implements TypeInterface
{
    public function __construct(
        protected readonly bool $strict = false
    ) {
    }

    public function cast(float|object|int|bool|array|string|null $value): bool|float|int|string|array|object|null
    {
        if (($error = (new BoolRule($this->strict))->validate($value)) === true) {
            return (bool)$value;
        }

        throw new ValidationException(['' => [$error]]);
    }
}
