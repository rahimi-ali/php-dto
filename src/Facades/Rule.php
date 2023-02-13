<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Facades;

use RahimiAli\PhpDto\Rules\InRule;
use RahimiAli\PhpDto\Rules\IntRule;
use RahimiAli\PhpDto\Rules\MaxRule;
use RahimiAli\PhpDto\Rules\MinRule;
use RahimiAli\PhpDto\Rules\BoolRule;
use RahimiAli\PhpDto\Rules\NotEqual;
use RahimiAli\PhpDto\Rules\ArrayRule;
use RahimiAli\PhpDto\Rules\FloatRule;
use RahimiAli\PhpDto\Rules\NotInRule;
use RahimiAli\PhpDto\Rules\EqualsRule;
use RahimiAli\PhpDto\Rules\ObjectRule;
use RahimiAli\PhpDto\Rules\StringRule;
use RahimiAli\PhpDto\Rules\MaxLengthRule;
use RahimiAli\PhpDto\Rules\MinLengthRule;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

#[CodeCoverageIgnore] // ignored because it's a simple facade with 0 logic
class Rule
{
    public static function int(bool $strict = false): IntRule
    {
        return new IntRule();
    }

    public static function float(bool $strict = false): FloatRule
    {
        return new FloatRule();
    }

    public static function string(bool $strict = false): StringRule
    {
        return new StringRule($strict);
    }

    public static function bool(bool $strict = false): BoolRule
    {
        return new BoolRule($strict);
    }

    public static function array(): ArrayRule
    {
        return new ArrayRule();
    }

    public static function object(): ObjectRule
    {
        return new ObjectRule();
    }

    public static function min(int $min, bool $strict = false): MinRule
    {
        return new MinRule($min, $strict);
    }

    public static function max(int $max, bool $strict = false): MaxRule
    {
        return new MaxRule($max, $strict);
    }

    public static function minLength(int $length, bool $strict = false): MinLengthRule
    {
        return new MinLengthRule($length, $strict);
    }

    public static function maxLength(int $length, bool $strict = false): MaxLengthRule
    {
        return new MaxLengthRule($length, $strict);
    }

    /**
     * @param array<mixed> $values
     */
    public static function in(array $values, bool $strict = false): InRule
    {
        return new InRule($values, $strict);
    }

    /**
     * @param array<mixed> $values
     */
    public static function notIn(array $values, bool $strict = false): NotInRule
    {
        return new NotInRule($values, $strict);
    }

    public static function equals(mixed $value, bool $strict = false): EqualsRule
    {
        return new EqualsRule($value, $strict);
    }

    public static function notEqual(mixed $value, bool $strict = false): NotEqual
    {
        return new NotEqual($value, $strict);
    }
}
