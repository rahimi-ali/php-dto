<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\IntRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IntRule::class)]
class IntRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('convertableToIntProvider')]
    #[DataProvider('notConvertableToIntProvider')]
    public function returnsValidationErrorIfStrictAndValueIsNotAnInt(mixed $var): void
    {
        $result = (new IntRule(true))->validate($var);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('int', $result->key);
    }

    #[Test]
    #[DataProvider('notConvertableToIntProvider')]
    public function returnsValidationErrorIfNotStrictAndValueCannotBeCastToInt(mixed $var): void
    {
        $result = (new IntRule())->validate($var);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('int', $result->key);
    }

    #[Test]
    #[DataProvider('intProvider')]
    public function returnsTrueIfStrictAndValueIsInt(mixed $var): void
    {
        $result = (new IntRule(true))->validate($var);

        $this->assertTrue($result);
    }

    #[Test]
    #[DataProvider('intProvider')]
    #[DataProvider('convertableToIntProvider')]
    public function returnsTrueIfNotStrictAndValueCanBeCastToInt(mixed $var): void
    {
        $result = (new IntRule())->validate($var);

        $this->assertTrue($result);
    }

    public static function intProvider(): array
    {
        return [
            '14500(int)' => [14500],
            '-13(int)' => [-13],
            '0(int)' => [0],
        ];
    }

    public static function convertableToIntProvider(): array
    {
        return [
            '\'1234\'' => ['1234'],
            '\'0\'' => ['0'],
            '\'-15\'' => ['-15'],
        ];
    }

    public static function notConvertableToIntProvider(): array
    {
        return [
            '\'random-text\'' => ['random-text'],
            '134.5(float)' => [134.5],
            '\'134.5\'' => ['134.5'],
            '\'2023-02-01\'' => ['2023-02-01'],
        ];
    }
}
