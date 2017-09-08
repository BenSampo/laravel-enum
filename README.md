This package adds support for creating enums in PHP and includes a generator for Laravel.

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
}
```

Values can now be accessed like so:
``` php
UserType::Moderator // Returns 1
```

## Methods

### getKeys()
### getValues()
### getKey(int $value)
### getValue(string $key)
### getDescription(int $value)
### getRandomKey
### getRandomValue

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
