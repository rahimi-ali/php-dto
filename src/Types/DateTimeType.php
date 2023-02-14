<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto\Types;

use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use RahimiAli\PhpDto\TypeInterface;
use RahimiAli\PhpDto\Rules\StringRule;
use RahimiAli\PhpDto\ValidationException;
use RahimiAli\PhpDto\Support\ValidationError;

class DateTimeType implements TypeInterface
{
    public function __construct(
        protected readonly string $format = DateTimeInterface::ATOM,
        protected readonly string|null $timezone = null,
        protected readonly bool $immutable = true,
    ) {
    }

    public function cast(float|object|int|bool|array|string|null $value): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (($validationError = (new StringRule(true))->validate($value)) !== true) {
            throw new ValidationException(['' => [$validationError]]);
        }

        /** @var string $value */

        $zone = $this->timezone ? new DateTimeZone($this->timezone) : null;

        $datetime = $this->immutable
            ? DateTimeImmutable::createFromFormat($this->format, $value, $zone)
            : DateTime::createFromFormat($this->format, $value, $zone);

        if ($datetime === false) {
            throw new ValidationException(['' => [new ValidationError('datetime')]]);
        }

        return $datetime;
    }
}
