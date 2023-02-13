<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\FloatRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(FloatRule::class)]
class FloatRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('convertableToFloatProvider')]
    #[DataProvider('notConvertableToFloatProvider')]
    public function returnsValidationErrorIfStrictAndValueIsNotAnFloat(mixed $var): void
    {
        $result = (new FloatRule(true))->validate($var);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('float', $result->key);
    }

    #[Test]
    #[DataProvider('notConvertableToFloatProvider')]
    public function returnsValidationErrorIfNotStrictAndValueCannotBeCastToFloat(mixed $var): void
    {
        $result = (new FloatRule())->validate($var);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('float', $result->key);
    }

    #[Test]
    #[DataProvider('floatProvider')]
    public function returnsTrueIfStrictAndValueIsFloat(mixed $var): void
    {
        $result = (new FloatRule(true))->validate($var);

        $this->assertTrue($result);
    }

    #[Test]
    #[DataProvider('floatProvider')]
    #[DataProvider('convertableToFloatProvider')]
    public function returnsTrueIfNotStrictAndValueCanBeCastToFloat(mixed $var): void
    {
        $result = (new FloatRule())->validate($var);

        $this->assertTrue($result);
    }

    public static function floatProvider(): array
    {
        return [
            '14500.0(float)' => [14500.0],
            '14500.57(float)' => [14500.57],
            '-13.0(float)' => [-13.0],
            '-13.78(float)' => [-13.78],
            '0.0(float)' => [0.0],
        ];
    }

    public static function convertableToFloatProvider(): array
    {
        return [
            '\'1234\'' => ['1234'],
            '\'1234.6\'' => ['1234.6'],
            '\'0\'' => ['0'],
            '\'-15\'' => ['-15'],
            '\'-15.67\'' => ['-15.67'],
            '1234(int)' => [1234],
            '-34(int)' => [-34],
            '0(int)' => [0],
        ];
    }

    public static function notConvertableToFloatProvider(): array
    {
        return [
            '\'random-text\'' => ['random-text'],
            '\'2023-02-01\'' => ['2023-02-01'],
        ];
    }
}
