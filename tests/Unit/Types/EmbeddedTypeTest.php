<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use RahimiAli\PhpDto\Dto;
use RahimiAli\PhpDto\Field;
use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\InRule;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Types\StringType;
use RahimiAli\PhpDto\Types\EmbeddedType;
use RahimiAli\PhpDto\Rules\MinLengthRule;
use RahimiAli\PhpDto\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;

#[CoversClass(EmbeddedType::class)]
class EmbeddedTypeTest extends TestCase
{
    #[Test]
    public function throwsValidationExceptionIfInputIsNotAssocArrayOrObject(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address' => 'ABCDE',
        ];

        try {
            new EmbeddedTypeTestUserDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'address' => [new ValidationError('object', [])],
            ], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function returnsInputObjectIfItIsInstanceOfEmbeddedDto(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address' => new EmbeddedTypeTestAddressDto([
                'country' => 'US',
                'street' => '123 Main St',
            ]),
        ];

        $dto = new EmbeddedTypeTestUserDto($input);

        $this->assertSame($input['address'], $dto->address);
    }

    #[Test]
    public function throwsValidationExceptionIfDtoValidationFails(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address' => [
                'country' => 'US',
                'street' => '123',
            ],
        ];

        try {
            new EmbeddedTypeTestUserDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'address.street' => [new ValidationError('minLength', ['minLength' => 5])],
            ], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function castsToEmbeddedDto(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address' => [
                'country' => 'US',
                'street' => '123 Main St',
            ],
        ];

        $dto = new EmbeddedTypeTestUserDto($input);

        $this->assertInstanceOf(EmbeddedTypeTestAddressDto::class, $dto->address);
        $this->assertSame('US', $dto->address->country);
        $this->assertSame('123 Main St', $dto->address->street);
    }
}

class EmbeddedTypeTestAddressDto extends Dto
{
    public string $country;
    public string $street;

    public static function fields(array $data): array
    {
        return [
            'country' => new Field('country', new StringType(), rules: [new InRule(['US', 'CA'])]),
            'street' => new Field('street', new StringType(), rules: [new MinLengthRule(5)]),
        ];
    }
}

class EmbeddedTypeTestUserDto extends Dto
{
    public string $firstName;
    public string $lastName;
    public EmbeddedTypeTestAddressDto $address;

    public static function fields(array $data): array
    {
        return [
            'firstName' => new Field('firstName', new StringType(), rules: [new MinLengthRule(3)]),
            'lastName' => new Field('lastName', new StringType(), rules: [new MinLengthRule(3)]),
            'address' => new Field('address', new EmbeddedType(EmbeddedTypeTestAddressDto::class)),
        ];
    }
}
