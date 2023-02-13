<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\BoolRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(BoolRule::class)]
class BoolRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('convertableToBoolProvider')]
    #[DataProvider('notConvertableToBoolProvider')]
    public function returnsValidationErrorIfStrictAndValueIsNotABoolean(mixed $var): void
    {
        $return = (new BoolRule(true))->validate($var);

        $this->assertInstanceOf(ValidationError::class, $return);
        $this->assertEquals($return->key, 'bool');
    }

    #[Test]
    #[DataProvider('notConvertableToBoolProvider')]
    public function returnsValidationErrorIfNotStrictAndValueCannotBeCastToBoolean(mixed $var): void
    {
        $return = (new BoolRule(false))->validate($var);

        $this->assertInstanceOf(ValidationError::class, $return);
        $this->assertEquals($return->key, 'bool');
    }

    #[Test]
    #[DataProvider('boolProvider')]
    public function returnsTrueIfStrictAndValueIsBoolean(mixed $var): void
    {
        $return = (new BoolRule(true))->validate($var);

        $this->assertTrue($return);
    }

    #[Test]
    #[DataProvider('boolProvider')]
    #[DataProvider('convertableToBoolProvider')]
    public function returnsTrueIfNotStrictAndValueCanBeCastToBoolean(mixed $var): void
    {
        $return = (new BoolRule(false))->validate($var);

        $this->assertTrue($return);
    }

    public static function convertableToBoolProvider(): array
    {
        return [
            '\'true\'' => ['true'],
            '\'false\'' => ['false'],
            '\'yes\'' => ['yes'],
            '\'no\'' => ['no'],
            '\'0\'' => ['0'],
            '\'1\'' => ['1'],
            '1(number)' => [1],
            '0(number)' => [0],
        ];
    }

    public static function boolProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }

    public static function notConvertableToBoolProvider(): array
    {
        return [
            '\'random-text\'' => ['random-text'],
            '123(number)' => [123],
        ];
    }
}
