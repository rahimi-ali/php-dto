<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\InRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(InRule::class)]
class InRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesIfValueIsInArray(array $acceptableValues, bool $strict, mixed $arg, bool $passes): void
    {
        $result = (new InRule($acceptableValues, $strict))->validate($arg);

        if ($passes) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('in', $result->key);
            $this->assertEquals(['values' => implode(', ', $acceptableValues)], $result->replacements);
        }
    }

    public static function dataProvider(): array
    {
        return [
            'strict and value is in array passes' => [[1, 2, 3], true, 1, true],
            'strict and value is not in array fails' => [[1, 2, 3], true, 4, false],
            'not strict and value is in array passes' => [[1, 2, 3], false, '1', true],
            'not strict and value is not in array fails' => [[1, 2, 3], false, '4', false],
        ];
    }
}
