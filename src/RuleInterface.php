<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto;

use RahimiAli\PhpDto\Support\ValidationError;

interface RuleInterface
{
    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     */
    public function validate(bool|float|int|string|array|object|null $value): true|ValidationError;
}
