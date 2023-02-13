<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\NotInRule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(NotInRule::class)]
class NotInRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesIfValueIsNotInArray(array $unacceptableValues, bool $strict, mixed $arg, bool $passes): void
    {
        $result = (new NotInRule($unacceptableValues, $strict))->validate($arg);

        if ($passes) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('notIn', $result->key);
            $this->assertEquals(['values' => implode(', ', $unacceptableValues)], $result->replacements);
        }
    }

    public static function dataProvider(): array
    {
        return [
            'strict and value is in array fails' => [[1, 2, 3], true, 1, false],
            'strict and value is not in array passes' => [[1, 2, 3], true, 4, true],
            'not strict and value is in array fails' => [[1, 2, 3], false, '1', false],
            'not strict and value is not in array passes' => [[1, 2, 3], false, '4', true],
        ];
    }
}
