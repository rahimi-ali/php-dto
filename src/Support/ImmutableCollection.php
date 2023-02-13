<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Support;

/**
 * Does not enforce immutability if there are object in the collection.
 *
 * The way to achieve immutability with object is to make the objects immutable
 * for example by making properties private and not having setters.
 *
 * @template T
 */
class ImmutableCollection
{
    /**
     * @param array<int, T> $items
     */
    public function __construct(
        private readonly array $items = [],
    ) {
    }

    /**
     * @return T
     */
    public function get(int $index): mixed
    {
        return $this->items[$index];
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param callable(T): mixed $callback
     * @return ImmutableCollection<mixed>
     */
    public function map(callable $callback): ImmutableCollection
    {
        return new ImmutableCollection(array_map($callback, $this->items));
    }

    /**
     * @param callable(T): bool $callback
     * @return ImmutableCollection<T>
     */
    public function filter(callable $callback): ImmutableCollection
    {
        return new ImmutableCollection(array_values(array_filter($this->items, $callback)));
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
