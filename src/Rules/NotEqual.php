<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Rules;

use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore] // This class is a simple wrapper around EqualsRule
class NotEqual extends EqualsRule
{
    protected const errorKey = 'notEqual';

    public function validate(bool|float|int|string|array|object|null $value): true|ValidationError
    {
        return $this->isEqual($value) ? $this->validationError() : true;
    }
}
