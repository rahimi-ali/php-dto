<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Rules\MaxLengthRule;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(MaxLengthRule::class)]
class MaxLengthRuleTest extends TestCase
{
    #[Test]
    public function throwsInvalidArgumentExceptionWhenMaxLengthIsNotZeroOrMore(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The max length must be zero or more.');

        new MaxLengthRule(-1);
    }

    #[Test]
    public function throwsInvalidArgumentExceptionWhenValueIsNotStringOrArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value must be a string or an array.');

        (new MaxLengthRule(0))->validate(1);
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesMinLength(int $maxLength, bool $strict, string|array $arg, bool $shouldPass): void
    {
        $result = (new MaxLengthRule($maxLength, $strict))->validate($arg);

        if ($shouldPass) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('maxLength' . ($strict ? '.strict' : ''), $result->key);
            $this->assertEquals(['maxLength' => $maxLength], $result->replacements);
        }
    }

    public static function dataProvider(): array
    {
        return [
            'strict and shorter string passes' => [10, true, 'abcdefghi', true],
            'strict and equal length string fails' => [10, true, 'abcdefghij', false],
            'strict and longer length string fails' => [10, true, 'abcdefghijk', false],
            'strict and shorter array passes' => [3, true, [1, 3], true],
            'strict and equal length array fails' => [3, true, [1, 3, 5], false],
            'strict and longer length array fails' => [3, true, ['a', 'b', 'c', 'd'], false],
            'not strict and shorter string passes' => [10, false, 'abcdefghi', true],
            'not strict and equal length string passes' => [10, false, 'abcdefghij', true],
            'not strict and longer length string fails' => [10, false, 'abcdefghijk', false],
            'not strict and shorter array passes' => [3, false, [1, 3], true],
            'not strict and equal length array passes' => [3, false, ['a', 3, 'g'], true],
            'not strict and longer length array fails' => [3, false, ['a', 'b', 'c', ['a']], false],
        ];
    }
}
