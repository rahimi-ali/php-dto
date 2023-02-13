<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit;

use RahimiAli\PhpDto\Dto;
use RahimiAli\PhpDto\Field;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\MaxRule;
use RahimiAli\PhpDto\Types\IntType;
use RahimiAli\PhpDto\Types\BoolType;
use Psr\Http\Message\StreamInterface;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;

#[CoversClass(Dto::class)]
class DtoTest extends TestCase
{
    #[Test]
    public function setsPropertiesAccordingToFieldProperty(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $dto = new TestDto($input);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function setsPropertiesAccordingToFieldPropertyWithDefault(): void
    {
        $input = ['foo' => 1];

        $dto = new TestDto($input);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function throwsExceptionWithRequiredKeyIfFieldIsNotNullableAndNotPresentInInput(): void
    {
        $input = ['bar' => true];

        try {
            new TestDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals(['foo' => [new ValidationError('required')]], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function throwsValidationExceptionIfTypeValidationFails(): void
    {
        $input = ['foo' => 1, 'bar' => 'not a bool'];

        try {
            new TestDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals(['bar' => [new ValidationError('bool')]], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function throwsValidationExceptionIfAnyFieldRuleFails(): void
    {
        $input = ['foo' => 12, 'bar' => false];

        try {
            new TestDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals(['foo' => [new ValidationError('max', ['max' => 10])]], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function canDeserializeJson(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $dto = TestDto::fromJson(json_encode($input));

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function canDeserializePsrServerRequestWithJsonContent(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturn(['application/json']);
        $body = $this->createStub(StreamInterface::class);
        $body->method('getContents')->willReturn(json_encode($input));
        $request->method('getBody')->willReturn($body);

        $dto = TestDto::fromServerRequest($request);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function canDeserializePsrServerRequestWithQueryParams(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getQueryParams')->willReturn($input);

        $dto = TestDto::fromServerRequest($request);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function canDeserializePsrServerRequestWithParsedBody(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturn(['application/x-www-form-urlencoded']);
        $request->method('getParsedBody')->willReturn($input);

        $dto = TestDto::fromServerRequest($request);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function throwsExceptionIfServerRequestCannotBeDeserialized(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getHeader')->willReturn(['whatever']);

        $this->expectException(InvalidArgumentException::class);

        TestDto::fromServerRequest($request);
    }
}

class TestDto extends Dto
{
    public int $foo;
    protected bool $baz;

    public static function fields(): array
    {
        return [
            'foo' => new Field('foo', new IntType(), rules: [new MaxRule(10)]),
            'bar' => new Field('baz', new BoolType(), nullable: true, default: true),
        ];
    }

    public function getBaz(): bool
    {
        return $this->baz;
    }
}
