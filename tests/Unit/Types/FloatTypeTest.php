<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Types\FloatType;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FloatType::class)]
class FloatTypeTest extends TestCase
{
    #[Test]
    public function throwsExceptionIfTypeValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $type = new FloatType();

        $type->cast('string');
    }

    #[Test]
    public function castsToFloat(): void
    {
        $type = new FloatType();

        $this->assertSame(123.45, $type->cast('123.45'));
    }
}
