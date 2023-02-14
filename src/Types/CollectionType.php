<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\Rules\ArrayRule;
use RahimiAli\PhpDto\ValidationException;
use RahimiAli\PhpDto\Support\ImmutableCollection;

class CollectionType implements TypeInterface
{
    public function __construct(
        protected readonly TypeInterface $type,
    ) {
    }

    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     * @return ImmutableCollection<array<mixed>|bool|float|int|object|string|null>
     * @throws ValidationException
     */
    public function cast(float|object|int|bool|array|string|null $value): ImmutableCollection
    {
        if ($value instanceof ImmutableCollection) {
            return $value;
        }

        if (($validationError = (new ArrayRule())->validate($value)) !== true) {
            throw new ValidationException(['' => [$validationError]]);
        }

        /** @var array<int, array<mixed>|bool|float|int|object|string|null> $value */

        $items = [];
        $validationErrors = [];

        foreach ($value as $index => $item) {
            try {
                $items[] = $this->type->cast($item);
            } catch (ValidationException $e) {
                $validationErrors = $validationErrors + array_combine(
                    array_map(
                        fn ($key) => $key === '' ? "$index" : "{$index}.{$key}",
                        array_keys($e->getErrors())
                    ),
                    array_values($e->getErrors())
                );
            }
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        return new ImmutableCollection($items);
    }
}
