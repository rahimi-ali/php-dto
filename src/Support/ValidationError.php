<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Support;

use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore] // ignored because it's a simple data class with 0 logic
class ValidationError
{
    /**
     * @param array<string, float|int|string> $replacements
     */
    public function __construct(
        public readonly string $key,
        public readonly array $replacements = [],
    ) {
    }
}
