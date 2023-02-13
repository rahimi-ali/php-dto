# PHP-DTO
## DTOs for PHP!

DTOs can be used to transform Incoming Psr7 server requests, json and random arrays to typed classes with validation built in.

Does not use reflection and tries to handle everything with good old classes and methods!

### Types
- `int(bool $strict = false): IntType`
- `float(bool $strict = false): FloatType`
- `string(bool $strict = false): StringType`
- `bool(bool $strict = false): BoolType`
- `embedded(string $class): EmbeddedType`
- `dynamicEmbedded(string|Closure $discriminator, array $types = []): DynamicEmbeddedType`

### Rules
- `int(bool $strict = false): IntRule` Automatically added when declaring a field with IntType
- `float(bool $strict = false): FloatRule` Automatically added when declaring a field with FloatType
- `string(bool $strict = false): StringRule` Automatically added when declaring a field with StringType
- `bool(bool $strict = false): BoolRule` Automatically added when declaring a field with BoolType
- `array(): ArrayRule` Should be an array with sequential int keys
- `object(): ObjectRule` Should be an object or an array with string keys or non-sequential int keys
- `min(int $min, bool $strict = false): MinRule`
- `max(int $max, bool $strict = false): MaxRule`
- `minLength(int $length, bool $strict = false): MinLengthRule`
- `maxLength(int $length, bool $strict = false): MaxLengthRule`
- `in(array $values, bool $strict = false): InRule`
- `notIn(array $values, bool $strict = false): NotInRule`
- `equals(mixed $value, bool $strict = false): EqualsRule`
- `notEqual(mixed $value, bool $strict = false): NotEqual`

### Notes
- Do not set readonly or private attributes for your DTO classes as the parent constructor which handles the hydration of DTOs cannot access them.


### License
MIT
