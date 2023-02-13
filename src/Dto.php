<?php

declare(strict_types=1);

namespace RahimiAli\PhpDto;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RahimiAli\PhpDto\Support\ValidationError;

/**
 * Do not set readonly and private properties if you want them to be set by the parent.
 */
abstract class Dto
{
    /**
     * @param array<string, array<mixed>|bool|float|int|object|string|null> $data
     * @throws ValidationException
     */
    final public function __construct(array $data = [])
    {
        $errors = [];

        foreach ($this->fields() as $key => $fieldDefinition) {
            $inputValue = $data[$key] ?? null;

            if ($inputValue !== null) {
                try {
                    $this->{$fieldDefinition->property} = $fieldDefinition->type->cast($inputValue);
                } catch (ValidationException $e) {
                    $errors = [...$errors, ...array_combine(
                        array_map(
                            fn ($nestedKey) => $nestedKey === '' ? $key : "$key.$nestedKey",
                            array_keys($e->getErrors())
                        ),
                        $e->getErrors()
                    )];

                    continue;
                }

                foreach ($fieldDefinition->rules as $rule) {
                    if (($error = $rule->validate($this->{$fieldDefinition->property})) !== true) {
                        $errors[$key][] = $error;
                    }
                }
            } else {
                if (!$fieldDefinition->nullable) {
                    $errors[$key][] = new ValidationError('required');
                } else {
                    $this->{$fieldDefinition->property} = $fieldDefinition->default;
                }
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }

    /**
     * @return array<string, Field>
     */
    abstract public static function fields(): array;

    public static function fromJson(string $json): static
    {
        return new static(json_decode($json, true)); /** @phpstan-ignore-line */
    }

    public static function fromServerRequest(ServerRequestInterface $request): static
    {
        if (in_array(strtoupper($request->getMethod()), ['GET', 'HEAD'], true)) {
            return new static($request->getQueryParams());
        } else {
            $contentType = $request->getHeader('Content-Type');
            $contentType = $contentType ? $contentType[0] : null;

            if ($contentType === 'application/json') {
                return static::fromJson((string) $request->getBody()->getContents());
            } elseif ($contentType === 'application/x-www-form-urlencoded') {
                return new static($request->getParsedBody()); /** @phpstan-ignore-line */
            }
        }

        throw new InvalidArgumentException('Unsupported request content type.');
    }
}
