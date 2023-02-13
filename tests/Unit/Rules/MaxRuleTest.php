<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\MaxRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(MaxRule::class)]
class MaxRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesMax(int|float $max, bool $strict, int|float $arg, bool $shouldPass): void
    {
        $result = (new MaxRule($max, $strict))->validate($arg);

        if ($shouldPass) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('max' . ($strict ? '.strict' : ''), $result->key);
            $this->assertEquals(['max' => $max], $result->replacements);
        }
    }

    public static function dataProvider(): array
    {
        return [
            'strict and bigger value fails' => [10, true, 11, false],
            'strict and equal value fails' => [10, true, 10, false],
            'strict and smaller value passes' => [10.2, true, 10.1, true],
            'not strict and bigger value fails' => [10, false, 10.1, false],
            'not strict and equal value passes' => [10, false, 10, true],
            'not strict and smaller value passes' => [10.2, false, 10.1, true],
        ];
    }
}
