# Laravel enum

This package adds support for creating enums in PHP and includes a generator for Laravel.

[![Travis Build Status](https://img.shields.io/travis/bensampo/laravel-enum.svg?style=flat-square)](https://travis-ci.org/bensampo/laravel-enum)
[![Packagist Stable Version](https://img.shields.io/packagist/v/bensampo/laravel-enum.svg?style=flat-square&label=stable)](https://packagist.org/packages/bensampo/laravel-enum)
[![Packagist downloads](https://img.shields.io/packagist/dt/bensampo/laravel-enum.svg?style=flat-square)](https://packagist.org/packages/bensampo/laravel-enum)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

## Guide
I wrote a blog post about using laravel-enum:
https://sampo.co.uk/blog/using-enums-in-laravel

## Install

Via Composer

``` bash
$ composer require bensampo/laravel-enum
```

If you're using Laravel < 5.5 you'll need to add the service provider to `config/app.php`
``` php
'BenSampo\Enum\EnumServiceProvider'
```

## Generating enums

```php
php artisan make:enum UserType
```

## Usage

Given the following enum:
``` php
namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;

    public static function getDescription(int $value): string
    {
        switch ($value) {
            case self::SuperAdministrator:
                return 'Super admin';
            break;
            default:
                return self::getKey($value);
        }
    }
}
```

Values can now be accessed like so:
``` php
UserType::Moderator // Returns 1
```

## Methods

### getKeys(): array

Returns an array of the keys for an enum.

``` php
UserType::getKeys(); // Returns ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator']
```

### getValues(): array

Returns an array of the values for an enum.

``` php
UserType::getValues(); // Returns [0, 1, 2, 3]
```

### getKey(int $value): string

Returns the key for the given enum value.

``` php
UserType::getKey(1); // Returns 'Moderator'
```

### getValue(string $key): int

Returns the value for the given enum key.

``` php
UserType::getValue('Moderator'); // Returns 1
```

### getDescription(int $value): string

Returns the description for the enum value.

``` php
UserType::getDescription(3); // Returns 'Super admin'
```

### getRandomKey(): string

Returns a random key from the enum. Useful for factories.

``` php
UserType::getRandomKey(); // Returns 'Administrator', 'Moderator', 'Subscriber' or 'SuperAdministrator'
```

### getRandomValue(): int

Returns a random value from the enum. Useful for factories.

``` php
UserType::getRandomValue(); // Returns 0, 1, 2 or 3
```

## Validation

You may validate that an enum value passed to a controller is a valid value for a given enum by using the `EnumValue` rule.

``` php
public function store(Request $request)
{
    $this->validate($request, [
        'user_type' => ['required', new EnumValue(UserType::class)],
    ]);
}
```

Of course, this works on form request classes too.

Make sure to include `BenSampo\Enum\Rules\EnumValue` and your enum class in the usings.
