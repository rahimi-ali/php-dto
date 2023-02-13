<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Types\StringType;
use RahimiAli\PhpDto\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StringType::class)]
class StringTypeTest extends TestCase
{
    #[Test]
    public function throwsExceptionIfTypeValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $type = new StringType();

        $type->cast([1]);
    }

    #[Test]
    public function castsToString(): void
    {
        $type = new StringType();

        $this->assertSame('123', $type->cast(123));
    }
}
