<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Types\IntType;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IntType::class)]
class IntTypeTest extends TestCase
{
    #[Test]
    public function throwsExceptionIfTypeValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $type = new IntType();

        $type->cast('not an integer');
    }

    #[Test]
    public function castsToInt(): void
    {
        $type = new IntType();

        $this->assertSame(123, $type->cast('123'));
    }
}
