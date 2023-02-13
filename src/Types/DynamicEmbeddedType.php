<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use Closure;
use RahimiAli\PhpDto\Dto;
use InvalidArgumentException;
use RahimiAli\PhpDto\Rules\InRule;
use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\Rules\ObjectRule;
use RahimiAli\PhpDto\ValidationException;
use RahimiAli\PhpDto\Support\ValidationError;

class DynamicEmbeddedType implements TypeInterface
{
    /**
     * @param string|Closure(array<string, mixed>|object $value): (class-string<Dto>|null) $discriminator null return means no match
     * @param array<mixed, class-string<Dto>> $types
     */
    public function __construct(
        protected readonly string|Closure $discriminator,
        protected readonly array $types = [],
    ) {
    }

    /**
     * @param array<mixed>|bool|float|int|object|string|null $value
     * @return Dto
     * @throws ValidationException
     */
    public function cast(bool|float|int|string|array|object|null $value): object
    {
        if ($value instanceof Dto) {
            return $value;
        }

        if (($validationError = (new ObjectRule())->validate($value)) !== true) {
            throw new ValidationException(['' => [$validationError]]);
        }

        /** @var array<string, array<mixed>|bool|float|int|object|string|null> $value */
        $value = (array) $value;

        if ($this->discriminator instanceof Closure) {
            $class = ($this->discriminator)($value);

            if ($class === null) {
                throw new ValidationException([
                    '' => [
                        new ValidationError('required'),
                    ],
                ]);
            }

            if (!is_string($class)) {
                throw new InvalidArgumentException('Closure should return a string.');
            }

            return new $class($value);
        } else {
            if (!isset($value[$this->discriminator])) {
                throw new ValidationException([
                    $this->discriminator => [
                        new ValidationError('required'),
                    ],
                ]);
            }

            $discriminatorValue = $value[$this->discriminator];

            if (($error = (new InRule(array_keys($this->types)))->validate($discriminatorValue)) !== true) {
                throw new ValidationException([$this->discriminator => [$error]]);
            }

            $class = $this->types[$discriminatorValue]; /** @phpstan-ignore-line */

            return new $class($value);
        }
    }
}
