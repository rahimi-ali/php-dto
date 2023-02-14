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

        $dto = new DtoTestDto($input);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function setsPropertiesAccordingToFieldPropertyWithDefault(): void
    {
        $input = ['foo' => 1];

        $dto = new DtoTestDto($input);

        $this->assertSame(1, $dto->foo);
        $this->assertSame(true, $dto->getBaz());
    }

    #[Test]
    public function throwsExceptionWithRequiredKeyIfFieldIsNotNullableAndNotPresentInInput(): void
    {
        $input = ['bar' => true];

        try {
            new DtoTestDto($input);
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
            new DtoTestDto($input);
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
            new DtoTestDto($input);
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

        $dto = DtoTestDto::fromJson(json_encode($input));

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

        $dto = DtoTestDto::fromServerRequest($request);

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

        $dto = DtoTestDto::fromServerRequest($request);

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

        $dto = DtoTestDto::fromServerRequest($request);

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

        DtoTestDto::fromServerRequest($request);
    }

    #[Test]
    public function beforeFillHookCanModifyInput(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $dto = new DtoTestDtoWithHooks($input);

        $this->assertSame(2, $dto->foo);
    }

    #[Test]
    public function afterFillHookCanModifyDto(): void
    {
        $input = ['foo' => 1, 'bar' => true];

        $dto = new DtoTestDtoWithHooks($input);

        $this->assertSame(false, $dto->getBaz());
    }

    #[Test]
    public function rawDataIsPassedToFieldsMethod(): void
    {
        $input = ['foo' => 665];

        $dto = new DtoTestDtoWithHooks($input);

        $this->assertSame(666, $dto->foo);
    }
}

class DtoTestDto extends Dto
{
    public int $foo;
    protected bool $baz;

    public static function fields(array $data): array
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

class DtoTestDtoWithHooks extends DtoTestDto
{
    public static function fields(array $data): array
    {
        if ($data['foo'] === 666) {
            return [
                'foo' => new Field('foo', new IntType(), rules: [new MaxRule(666)]),
            ];
        }

        return parent::fields($data);
    }

    public function beforeFill(array $data): array
    {
        $data['foo']++;

        return $data;
    }

    public function afterFill(): void
    {
        if (isset($this->baz)) {
            $this->baz = !$this->baz;
        }
    }
}
