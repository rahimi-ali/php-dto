<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use RahimiAli\PhpDto\Support\ImmutableCollection;

#[CoversClass(ImmutableCollection::class)]
class ImmutableCollectionTest extends TestCase
{
    #[Test]
    public function canGetByIndex(): void
    {
        $collection = new ImmutableCollection([1, 2, 'a']);

        $this->assertEquals(1, $collection->get(0));
        $this->assertEquals(2, $collection->get(1));
        $this->assertEquals('a', $collection->get(2));
    }

    #[Test]
    public function canCount(): void
    {
        $collection = new ImmutableCollection([1, 2, 'a']);

        $this->assertEquals(3, $collection->count());
    }

    #[Test]
    public function canMap(): void
    {
        $collection = new ImmutableCollection([1, 2, 3]);

        $mapped = $collection->map(fn ($item) => $item * $item);

        $this->assertInstanceOf(ImmutableCollection::class, $mapped);
        $this->assertEquals([1, 4, 9], $mapped->toArray());
    }

    #[Test]
    public function canFilter(): void
    {
        $collection = new ImmutableCollection([1, 2, 3]);

        $filtered = $collection->filter(fn ($item) => $item % 2 === 1);

        $this->assertInstanceOf(ImmutableCollection::class, $filtered);

        $this->assertEquals([1, 3], $filtered->toArray());
    }

    #[Test]
    public function canConvertToArray(): void
    {
        $collection = new ImmutableCollection([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $collection->toArray());
    }
}
