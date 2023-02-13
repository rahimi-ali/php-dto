<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto;

interface TypeInterface
{
    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     * @return array<mixed>|bool|float|int|object|string|null
     * @throws ValidationException
     */
    public function cast(bool|float|int|string|array|object|null $value): bool|float|int|string|array|object|null;
}
