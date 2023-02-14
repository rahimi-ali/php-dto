<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Types\DateTimeType;
use RahimiAli\PhpDto\ValidationException;
use RahimiAli\PhpDto\Support\ValidationError;

class DateTimeTypeTest extends TestCase
{
    #[Test]
    public function throwsExceptionIfInputIsNotAString(): void
    {
        $type = new DateTimeType('Y-m-d');

        try {
            $type->cast(12);
        } catch (ValidationException $e) {
            $this->assertEquals(['' => [new ValidationError('string')]], $e->getErrors());

            return;
        }

        $this->fail('No ValidationException was thrown.');
    }

    #[Test]
    public function returnsInputIfItIsOfDateTimeInterfaceType(): void
    {
        $input = new DateTime();

        $type = new DateTimeType('Y-m-d');

        $this->assertSame($input, $type->cast($input));
    }

    #[Test]
    public function throwsExceptionIfStringCannotBeParsedAsDateTimeWithGivenFormat(): void
    {
        $type = new DateTimeType('Y-m-d');

        try {
            $type->cast('foo');
        } catch (ValidationException $e) {
            $this->assertEquals(['' => [new ValidationError('datetime')]], $e->getErrors());

            return;
        }

        $this->fail('No ValidationException was thrown.');
    }

    #[Test]
    public function returnsImmutableDateTimeIfImmutableFlagIsTrue(): void
    {
        $type = new DateTimeType('Y-m-d', immutable: true);

        $result = $type->cast('2021-01-01');

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals('2021-01-01', $result->format('Y-m-d'));
    }

    #[Test]
    public function returnsDateTimeIfImmutableFlagIsFalse(): void
    {
        $type = new DateTimeType('Y-m-d', immutable: false);

        $result = $type->cast('2021-01-01');

        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2021-01-01', $result->format('Y-m-d'));
    }
}
