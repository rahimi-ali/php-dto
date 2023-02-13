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

### Example
```php
class AddressDto extends Dto
{
    private string $city;
    private string $street;
    private int $number;
    
    public static function fields(): array
    {
        return [
            'city' => new Field('city', Type::string(true), rules: [Rule::in(['London', 'Paris', 'New York'])]),
            'street' => new Field('street', Type::string(true), rules: [Rule::min(5)]),
            'number' => new Field('number', Type::int(true), rules: [Rule::min(1), Rule::max(100)]),
        ];
    }
    
    public function getCity(): string
    {
        return $this->city;
    }
    
    public function getStreet(): string
    {
        return $this->street;
    }
    
    public function getNumber(): int
    {
        return $this->number;
    }
}

class UserDto extends Dto
{
    private string $name;
    private int $age;
    private AddressDto $address;
    
    public static function fields(): array
    {
        return [
            'name' => new Field('name', Type::string(true), rules: [Rule::min(5)]),
            'age' => new Field('age', Type::int(true), rules: [Rule::min(18)]),
            'address' => new Field('address', Type::embedded(AddressDto::class)),
        ];
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getAge(): int
    {
        return $this->age;
    }
    
    public function getAddress(): AddressDto
    {
        return $this->address;
    }
}
```

### Notes
- Do not set readonly or private attributes for your DTO classes as the parent constructor which handles the hydration of DTOs cannot access them.


### License
MIT
