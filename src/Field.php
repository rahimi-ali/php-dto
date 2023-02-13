<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto;

use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore] // ignored because it's a simple data class with 0 logic
class Field
{
    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(
        public string $property,
        public TypeInterface $type,
        public bool $nullable = false,
        public array $rules = [],
        public mixed $default = null,
    ) {
    }
}
