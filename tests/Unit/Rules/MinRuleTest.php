<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\MinRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(MinRule::class)]
class MinRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesMin(int|float $min, bool $strict, int|float $arg, bool $shouldPass): void
    {
        $result = (new MinRule($min, $strict))->validate($arg);

        if ($shouldPass) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('min' . ($strict ? '.strict' : ''), $result->key);
            $this->assertEquals(['min' => $min], $result->replacements);
        }
    }

    public static function dataProvider(): array
    {
        return [
            'strict and smaller value fails' => [10, true, 9, false],
            'strict and equal value fails' => [10, true, 10, false],
            'strict and greater value passes' => [10.1, true, 10.2, true],
            'not strict and smaller value fails' => [10.1, false, 10.0, false],
            'not strict and equal value passes' => [10, false, 10, true],
            'not strict and greater value passes' => [10.1, false, 10.2, true],
        ];
    }
}
