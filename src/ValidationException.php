<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto;

use Exception;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore] // ignored because it's a simple exception with 0 logic
class ValidationException extends Exception
{
    /**
     * @param array<string, ValidationError[]> $errors
     */
    public function __construct(private array $errors)
    {
        parent::__construct();
    }

    /**
     * @return array<string, ValidationError[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
