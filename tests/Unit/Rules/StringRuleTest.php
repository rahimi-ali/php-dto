<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Rules\StringRule;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(StringRule::class)]
class StringRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('convertableToStringProvider')]
    #[DataProvider('notConvertableToStringProvider')]
    public function returnsValidationErrorIfStrictAndValueIsNotAnString(mixed $var): void
    {
        $result = (new StringRule(true))->validate($var);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('string', $result->key);
    }

    #[Test]
    #[DataProvider('notConvertableToStringProvider')]
    public function returnsValidationErrorIfNotStrictAndValueCannotBeCastToString(mixed $var): void
    {
        $result = (new StringRule())->validate($var);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('string', $result->key);
    }

    #[Test]
    #[DataProvider('stringProvider')]
    public function returnsTrueIfStrictAndValueIsString(mixed $var): void
    {
        $result = (new StringRule(true))->validate($var);

        $this->assertTrue($result);
    }

    #[Test]
    #[DataProvider('stringProvider')]
    #[DataProvider('convertableToStringProvider')]
    public function returnsTrueIfNotStrictAndValueCanBeCastToString(mixed $var): void
    {
        $result = (new StringRule())->validate($var);

        $this->assertTrue($result);
    }

    public static function stringProvider(): array
    {
        return [
            '"random-text"' => ['random-text'],
            '"123"' => ['123'],
            '"1456.0"' => ['1456.0'],
            '"true"' => ['true'],
        ];
    }

    public static function convertableToStringProvider(): array
    {
        return [
            '1234(int)' => [1234],
            '0(int)' => [0],
            '-1456.4(float)' => [-1456.4],
            'true' => [true],
            'false' => [false],
        ];
    }

    public static function notConvertableToStringProvider(): array
    {
        return [
            '[1, 2]' => [[1, 2]],
            '{"foo": "bar"}' => [['foo' => 'bar']],
        ];
    }
}
