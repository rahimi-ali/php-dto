<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Facades;

use Closure;
use RahimiAli\PhpDto\Dto;
use RahimiAli\PhpDto\Types\IntType;
use RahimiAli\PhpDto\Types\BoolType;
use RahimiAli\PhpDto\Types\FloatType;
use RahimiAli\PhpDto\Types\StringType;
use RahimiAli\PhpDto\Types\EmbeddedType;
use RahimiAli\PhpDto\Types\DynamicEmbeddedType;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore] // ignored because it's a simple facade with 0 logic
class Type
{
    public static function int(bool $strict = false): IntType
    {
        return new IntType($strict);
    }

    public static function float(bool $strict = false): FloatType
    {
        return new FloatType($strict);
    }

    public static function string(bool $strict = false): StringType
    {
        return new StringType($strict);
    }

    public static function bool(bool $strict = false): BoolType
    {
        return new BoolType($strict);
    }

    /**
     * @template T of Dto
     * @param class-string<T> $class
     * @return EmbeddedType<T>
     */
    public static function embedded(string $class): EmbeddedType
    {
        return new EmbeddedType($class);
    }

    /**
     * @param string|Closure(array<string, mixed>|object $value): (class-string<Dto>|null) $discriminator
     * @param array<mixed, class-string<Dto>> $types
     */
    public static function dynamicEmbedded(string|Closure $discriminator, array $types = []): DynamicEmbeddedType
    {
        return new DynamicEmbeddedType($discriminator, $types);
    }
}
