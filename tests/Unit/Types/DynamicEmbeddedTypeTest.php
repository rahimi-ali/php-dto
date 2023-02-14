<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use RahimiAli\PhpDto\Dto;
use RahimiAli\PhpDto\Field;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Types\StringType;
use RahimiAli\PhpDto\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use RahimiAli\PhpDto\Types\DynamicEmbeddedType;

#[CoversClass(DynamicEmbeddedType::class)]
class DynamicEmbeddedTypeTest extends TestCase
{
    #[Test]
    public function throwsExceptionIfInputIsNotAssocArrayOrObject(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => 'ABCDE',
        ];

        try {
            new DynamicEmbeddedTypeTestUserDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'contact' => [new ValidationError('object', [])],
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
            'contact' => new DynamicEmbeddedTypeTestPhoneDto([
                'type' => 'phone',
                'phoneNumber' => '1234567890',
            ]),
        ];

        $dto = new DynamicEmbeddedTypeTestUserDto($input);

        $this->assertSame($input['contact'], $dto->contact);
    }

    #[Test]
    public function returnsCorrectDtoBasedOnSimpleDiscriminatorValue(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'type' => 'phone',
                'phoneNumber' => '1234567890',
            ],
        ];

        $dto = new DynamicEmbeddedTypeTestUserDto($input);

        $this->assertInstanceOf(DynamicEmbeddedTypeTestPhoneDto::class, $dto->contact);
        $this->assertEquals($input['contact']['phoneNumber'], $dto->contact->number);

        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'type' => 'email',
                'email' => 'test@test.com',
            ],
        ];

        $dto = new DynamicEmbeddedTypeTestUserDto($input);

        $this->assertInstanceOf(DynamicEmbeddedTypeTestEmailDto::class, $dto->contact);
        $this->assertEquals($input['contact']['email'], $dto->contact->email);
    }

    #[Test]
    public function throwsExceptionIfDiscriminatorIsNotSet(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'phoneNumber' => '1234567890',
            ],
        ];

        try {
            new DynamicEmbeddedTypeTestUserDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'contact.type' => [new ValidationError('required', [])],
            ], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function throwsExceptionIfDiscriminatorIsNotInAllowedValues(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'type' => 'invalid',
                'phoneNumber' => '1234567890',
            ],
        ];

        try {
            new DynamicEmbeddedTypeTestUserDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'contact.type' => [new ValidationError('in', ['values' => 'phone, email'])],
            ], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function returnsCorrectDtoBasedOnClosureDiscriminator(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'phoneNumber' => '1234567890',
            ],
        ];

        $dto = new DynamicEmbeddedTypeTestUserDtoClosure($input);

        $this->assertInstanceOf(DynamicEmbeddedTypeTestPhoneDto::class, $dto->contact);
        $this->assertEquals($input['contact']['phoneNumber'], $dto->contact->number);

        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'email' => 'test@test.com',
            ],
        ];

        $dto = new DynamicEmbeddedTypeTestUserDtoClosure($input);

        $this->assertInstanceOf(DynamicEmbeddedTypeTestEmailDto::class, $dto->contact);
        $this->assertEquals($input['contact']['email'], $dto->contact->email);
    }

    #[Test]
    public function throwsExceptionIfNullIsReturnedFromDiscriminatorClosure(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'invalid' => '1234567890',
            ],
        ];

        try {
            new DynamicEmbeddedTypeTestUserDtoClosure($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'contact' => [new ValidationError('required', [])],
            ], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }

    #[Test]
    public function discriminatorClosureMustReturnStringOrNull(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'returnInt' => '1234567890',
            ],
        ];

        $this->expectException(InvalidArgumentException::class);

        new DynamicEmbeddedTypeTestUserDtoClosure($input);
    }

    #[Test]
    public function throwsExceptionIfEmbeddedDtoHasValidationException(): void
    {
        $input = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'contact' => [
                'type' => 'phone',
                'phoneNumber' => [1, 2], // should be string
            ],
        ];

        try {
            new DynamicEmbeddedTypeTestUserDto($input);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'contact.phoneNumber' => [new ValidationError('string', [])],
            ], $exception->getErrors());

            return;
        }

        $this->fail('ValidationException not thrown');
    }
}

class DynamicEmbeddedTypeTestPhoneDto extends Dto
{
    public string $number;

    public static function fields(array $data): array
    {
        return [
            'phoneNumber' => new Field('number', new StringType()),
        ];
    }
}

class DynamicEmbeddedTypeTestEmailDto extends Dto
{
    public string $email;

    public static function fields(array $data): array
    {
        return [
            'email' => new Field('email', new StringType()),
        ];
    }
}

class DynamicEmbeddedTypeTestUserDto extends Dto
{
    public string $firstName;
    public string $lastName;
    public DynamicEmbeddedTypeTestPhoneDto|DynamicEmbeddedTypeTestEmailDto $contact;

    public static function fields(array $data): array
    {
        return [
            'firstName' => new Field('firstName', new StringType()),
            'lastName' => new Field('lastName', new StringType()),
            'contact' => new Field(
                'contact',
                new DynamicEmbeddedType(
                    'type',
                    [
                        'phone' => DynamicEmbeddedTypeTestPhoneDto::class,
                        'email' => DynamicEmbeddedTypeTestEmailDto::class,
                    ]
                )
            ),
        ];
    }
}

class DynamicEmbeddedTypeTestUserDtoClosure extends Dto
{
    public string $firstName;
    public string $lastName;
    public DynamicEmbeddedTypeTestPhoneDto|DynamicEmbeddedTypeTestEmailDto $contact;

    public static function fields(array $data): array
    {
        return [
            'firstName' => new Field('firstName', new StringType()),
            'lastName' => new Field('lastName', new StringType()),
            'contact' => new Field(
                'contact',
                new DynamicEmbeddedType(
                    fn (array|object $input) => match (true) {
                        isset($input['phoneNumber']) => DynamicEmbeddedTypeTestPhoneDto::class,
                        isset($input['email']) => DynamicEmbeddedTypeTestEmailDto::class,
                        isset($input['returnInt']) => 12,
                        default => null,
                    }
                )
            ),
        ];
    }
}
