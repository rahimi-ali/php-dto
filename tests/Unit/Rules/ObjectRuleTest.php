<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use stdClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Rules\ObjectRule;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(ObjectRule::class)]
class ObjectRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('notObjectProvider')]
    public function returnsValidationErrorIfValueIsNotAnObject(mixed $arg): void
    {
        $result = (new ObjectRule())->validate($arg);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('object', $result->key);
    }

    #[Test]
    #[DataProvider('objectProvider')]
    public function returnsTrueIfValueIsAnObject(mixed $arg): void
    {
        $result = (new ObjectRule())->validate($arg);

        $this->assertTrue($result);
    }

    public static function notObjectProvider(): array
    {
        return [
            'string' => ['string'],
            'int' => [1],
            'float' => [1.0],
            'bool' => [true],
            'array with sequential numeric keys' => [[1, 2, '-4']],
            'null' => [null],
        ];
    }

    public static function objectProvider(): array
    {
        return [
            'array with string keys' => [['key' => 'value'], true],
            'object' => [new stdClass(), true],
            'array with non sequential numeric keys' => [[1 => 1, 3 => 2, 5 => '-4'], true],
        ];
    }
}
