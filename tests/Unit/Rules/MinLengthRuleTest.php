<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Rules\MinLengthRule;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(MinLengthRule::class)]
class MinLengthRuleTest extends TestCase
{
    #[Test]
    public function throwsInvalidArgumentExceptionWhenMinLengthIsNotZeroOrMore(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The min length must be zero or more.');

        new MinLengthRule(-1);
    }

    #[Test]
    public function throwsInvalidArgumentExceptionWhenValueIsNotStringOrArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a string or an array.');

        (new MinLengthRule(0))->validate(1);
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesMinLength(int $minLength, bool $strict, string|array $arg, bool $shouldPass): void
    {
        $result = (new MinLengthRule($minLength, $strict))->validate($arg);

        if ($shouldPass) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('minLength' . ($strict ? '.strict' : ''), $result->key);
            $this->assertEquals(['minLength' => $minLength], $result->replacements);
        }
    }

    public static function dataProvider(): array
    {
        return [
            'strict and longer string passes' => [10, true, 'abcdefghijk', true],
            'strict and equal length string fails' => [10, true, 'abcdefghij', false],
            'strict and shorter length string fails' => [10, true, 'abcdefgh', false],
            'strict and longer array passes' => [3, true, [1, 3, 5, 7], true],
            'strict and equal length array fails' => [3, true, [1, 3, 5], false],
            'strict and shorter length array fails' => [3, true, ['a', 'b'], false],
            'not strict and longer string passes' => [10, false, 'abcdefghijk', true],
            'not strict and equal length string passes' => [10, false, 'abcdefghij', true],
            'not strict and shorter length string fails' => [10, false, 'abcdefgh', false],
            'not strict and longer array passes' => [3, false, [1, 3, 5, 7], true],
            'not strict and equal length array passes' => [3, false, ['a', 3, 'g'], true],
            'not strict and shorter length array fails' => [3, false, ['a', 'b'], false],
        ];
    }
}
