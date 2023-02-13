<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Rules;

use stdClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Rules\EqualsRule;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(EqualsRule::class)]
class EqualsRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('dataProvider')]
    public function validatesEquality(
        mixed $expectedValue,
        bool $strict,
        mixed $arg,
        bool $shouldPass,
        string|null $errorDisplayValue
    ): void {
        $result = (new EqualsRule($expectedValue, $strict))->validate($arg);

        if ($shouldPass) {
            $this->assertTrue($result);
        } else {
            $this->assertInstanceOf(ValidationError::class, $result);
            $this->assertEquals('equals', $result->key);
            $this->assertEquals(['value' => $errorDisplayValue], $result->replacements);
        }
    }

    #[Test]
    public function usesClassToStringMethodForErrorDisplayValueIfItExists(): void
    {
        $class = new class () {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        $obj1 = new $class();
        $obj1->foo = 'bar';
        $obj2 = new $class();
        $obj2->foo = 'baz';

        $result = (new EqualsRule($obj1, false))->validate($obj2);

        $this->assertInstanceOf(ValidationError::class, $result);
        $this->assertEquals('equals', $result->key);
        $this->assertEquals(['value' => $obj1->__toString()], $result->replacements);
    }

    public static function dataProvider(): array
    {
        $obj1 = new stdClass();
        $obj1->foo = 'bar';
        $obj2 = new stdClass();
        $obj2->foo = 'bar';
        $obj3 = new stdClass();
        $obj3->foo = 'baz';

        return [
            '"1" !== 1' => [1, true, '1', false, '1'],
            '"1" == 1' => [1, false, '1', true, null],
            '1 === 1' => [1, true, 1, true, null],
            '1 == 1' => [1, false, 1, true, null],
            '"34.5" !== 34.5' => [-34.5, true, '-34.5', false, '-34.5'],
            '"34.5" == 34.5' => [-34.5, false, '-34.5', true, null],
            '-34.5 === -34.5' => [-34.5, true, -34.5, true, null],
            '-34.5 == -34.5' => [-34.5, false, -34.5, true, null],
            '-12 !== "-12"' => ['-12', true, -12, false, '\'-12\''],
            '"true" !== true' => [true, true, 'true', false, 'true'],
            '"true" == true' => [true, false, 'true', true, null],
            'true === true' => [true, true, true, true, null],
            'true == true' => [true, false, true, true, null],
            'true !== false' => [false, true, true, false, 'false'],
            '[1, 2] !== [2, 1]' => [[1, 2], true, [2, 1], false, '[1,2]'],
            '[1, 2] == [2, 1]' => [[1, 2], false, [2, 1], true, null],
            '[1, 2] === [1, 2]' => [[1, 2], true, [1, 2], true, null],
            '[1, 2] == [1, 2]' => [[1, 2], false, [1, 2], true, null],
            '["a" => 1, "b" => 2] === ["b" => 2, "a" => 1]' => [['a' => 1, 'b' => 2], false, ['b' => 2, 'a' => 1], true, null],
            'null !== 0' => [0, true, null, false, '0'],
            'null == 0' => [0, false, null, true, null],
            'null !== ""' => ['', true, null, false, '\'\''],
            'null == ""' => ['', false, null, true, null],
            '{"foo": "bar"} === {"foo": "bar"}' => [$obj1, true, $obj2, true, null],
            '{"foo": "bar"} == {"foo": "bar"}' => [$obj1, false, $obj2, true, null],
            '{"foo": "bar"} !== {"foo": "baz"}' => [$obj1, true, $obj3, false, '{"foo":"bar"}'],
            '{"foo": "bar"} A= {"foo": "baz"}' => [$obj1, false, $obj3, false, '{"foo":"bar"}'],
        ];
    }
}
