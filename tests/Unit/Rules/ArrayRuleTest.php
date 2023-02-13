<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use stdClass;
use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\ArrayRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(ArrayRule::class)]
class ArrayRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('notArrayProvider')]
    public function returnsValidationErrorIfValueIsNotAnArray(mixed $arg): void
    {
        $result = (new ArrayRule())->validate($arg);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('array', $result->key);
    }

    #[Test]
    #[DataProvider('arrayProvider')]
    public function returnsTrueIfValueIsAnArray(mixed $arg): void
    {
        $result = (new ArrayRule())->validate($arg);

        $this->assertTrue($result);
    }

    public static function notArrayProvider(): array
    {
        return [
            'string' => ['string'],
            'int' => [1],
            'float' => [1.0],
            'bool' => [true],
            'array with non sequential numeric keys' => [[1 => 1, 3 => 2, 5 => '-4']],
            'array with string keys' => [['key' => 'value'], true],
            'object' => [new stdClass(), true],
            'null' => [null],
        ];
    }

    public static function arrayProvider(): array
    {
        return [
            'array with sequential numeric keys' => [[1, 2, '-4'], true],
        ];
    }
}
