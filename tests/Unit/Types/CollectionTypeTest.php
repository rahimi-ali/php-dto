<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Types;

use RahimiAli\PhpDto\Dto;
use RahimiAli\PhpDto\Field;
use PHPUnit\Framework\TestCase;
use RahimiAli\PhpDto\Rules\MinRule;
use RahimiAli\PhpDto\Types\IntType;
use PHPUnit\Framework\Attributes\Test;
use RahimiAli\PhpDto\Types\EmbeddedType;
use RahimiAli\PhpDto\ValidationException;
use RahimiAli\PhpDto\Types\CollectionType;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ValidationError;
use RahimiAli\PhpDto\Support\ImmutableCollection;

#[CoversClass(CollectionType::class)]
class CollectionTypeTest extends TestCase
{
    #[Test]
    public function throwsValidationExceptionIfInputIsNotAnArray(): void
    {
        $type = new CollectionType(new IntType());

        try {
            $type->cast(['foo' => 'bar']);
        } catch (ValidationException $e) {
            $this->assertEquals(['' => [new ValidationError('array')]], $e->getErrors());

            return;
        }

        $this->fail('No ValidationException was thrown.');
    }

    #[Test]
    public function throwsValidationExceptionIfSomeItemsAreInvalid(): void
    {
        $failed = 0;

        $type = new CollectionType(new IntType());
        try {
            $type->cast([1, 'foo', 2, 'bar']);
        } catch (ValidationException $e) {
            $this->assertEquals([
                '1' => [new ValidationError('int')],
                '3' => [new ValidationError('int')],
            ], $e->getErrors());
            $failed++;
        }

        $type = new CollectionType(new EmbeddedType(CollectionTypeTestEmbeddedDto::class));
        try {
            $type->cast([
                ['id' => 1, 'quantity' => 2],
                ['id' => 'foo', 'quantity' => 3],
            ]);
        } catch (ValidationException $e) {
            $this->assertEquals([
                '1.id' => [new ValidationError('int')],
            ], $e->getErrors());
            $failed++;
        }

        $this->assertEquals(2, $failed);
    }

    #[Test]
    public function returnsImmutableCollectionIfInputIsValid(): void
    {
        $type = new CollectionType(new IntType());
        $result = $type->cast([1, 2, 3]);
        $this->assertInstanceOf(ImmutableCollection::class, $result);
        $this->assertEquals([1, 2, 3], $result->toArray());

        $type = new CollectionType(new EmbeddedType(CollectionTypeTestEmbeddedDto::class));
        $result = $type->cast([
            ['id' => 1, 'quantity' => 2],
            ['id' => 2, 'quantity' => 3],
        ]);

        $this->assertInstanceOf(ImmutableCollection::class, $result);
        $this->assertInstanceOf(CollectionTypeTestEmbeddedDto::class, $result->get(0));
        $this->assertInstanceOf(CollectionTypeTestEmbeddedDto::class, $result->get(1));
        $this->assertEquals(1, $result->get(0)->id);
        $this->assertEquals(2, $result->get(0)->quantity);
        $this->assertEquals(2, $result->get(1)->id);
        $this->assertEquals(3, $result->get(1)->quantity);
    }

    #[Test]
    public function returnsInputIfInputIsImmutableCollection(): void
    {
        $type = new CollectionType(new IntType());

        $immutableCollection = new ImmutableCollection([1, 2, 3]);

        $this->assertSame($immutableCollection, $type->cast($immutableCollection));
    }
}

class CollectionTypeTestEmbeddedDto extends Dto
{
    public static function fields(array $data): array
    {
        return [
            'id' => new Field('id', new IntType(true), rules: [new MinRule(1)]),
            'quantity' => new Field('quantity', new IntType(true), rules: [new MinRule(1)]),
        ];
    }
}
