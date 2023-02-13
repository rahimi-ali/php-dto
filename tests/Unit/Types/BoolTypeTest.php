<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Types\BoolType;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BoolType::class)]
class BoolTypeTest extends TestCase
{
    #[Test]
    public function throwsExceptionIfTypeValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $type = new BoolType();

        $type->cast('not a boolean');
    }

    #[Test]
    public function castsToBool(): void
    {
        $type = new BoolType();

        $this->assertTrue($type->cast(1));
    }
}
