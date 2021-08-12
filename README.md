<p align="center"><img src="https://github.com/BenSampo/laravel-enum/raw/master/branding/logo.svg?sanitize=true" alt="Laravel Enum" width="250" style="margin-bottom: 20px"></p>
<p align="center">
<a href="https://travis-ci.org/BenSampo/laravel-enum"><img src="https://travis-ci.org/BenSampo/laravel-enum.svg?branch=master" alt="Build Status"></a>
<a href="https://packagist.org/packages/bensampo/laravel-enum"><img src="https://img.shields.io/packagist/v/bensampo/laravel-enum.svg?style=flat-square&label=stable" alt="Packagist Stable Version"></a>
<a href="https://packagist.org/packages/bensampo/laravel-enum"><img src="https://img.shields.io/packagist/dt/bensampo/laravel-enum.svg?style=flat-square" alt="Packagist downloads"></a>
<a href="LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square" alt="MIT Software License"></a>
</p>

## About Laravel Enum

Simple, extensible and powerful enumeration implementation for Laravel.

- Enum key value pairs as class constants
- Full featured suite of methods
- Enum instantiation
- Flagged/Bitwise enums
- Type hinting
- Attribute casting
- Enum artisan generator
- Validation rules for passing enum key or values as input parameters
- Localization support
- Extendable via Macros

Created by [Ben Sampson](https://sampo.co.uk)

## Jump To

- [Guide](#guide)
- [Installation](#installation)
- [Enum Library](enum-library.md)
- [Basic Usage](#basic-usage)
  - [Enum definition](#enum-definition)
  - [Instantiation](#instantiation)
  - [Instance Properties](#instance-properties)
  - [Instance Equality](#instance-equality)
  - [Type Hinting](#instance-equality)
- [Flagged/Bitwise Enum](#flaggedbitwise-enum)
- [Attribute Casting](#attribute-casting)
- [Migrations](#migrations)
- [Validation](#validation)
- [Localization](#localization)
- [Overriding the getDescription method](#overriding-the-getdescription-method)
- [Extending the Enum Base Class](#extending-the-enum-base-class)
- [Laravel Nova Integration](#laravel-nova-integration)
- [PHPStan Integration](#phpstan-integration)
- [Artisan Command List](#artisan-command-list)
- [Enum Class Reference](#enum-class-reference)
- [Stubs](#stubs)

## Documentation for older versions

You are reading the documentation for `3.x`.

- If you're using **Laravel 7** or below, please see the [docs for `2.x`](https://github.com/BenSampo/laravel-enum/blob/v2.2.0/README.md).
- If you're using **Laravel 6** or below, please see the [docs for `1.x`](https://github.com/BenSampo/laravel-enum/blob/v1.38.0/README.md).

Please see the [upgrade guide](./UPGRADE.md) for information on how to upgrade to the latest version.

## Guide

I wrote a blog post about using laravel-enum: https://sampo.co.uk/blog/using-enums-in-laravel

## Installation

### Requirements

- Laravel `8` or higher
- PHP `7.3` or higher

Via Composer

```bash
composer require bensampo/laravel-enum
```

## Enum Library

Browse and download from a list of commonly used, community contributed enums.

[Enum library →](enum-library.md)

## Basic Usage

### Enum Definition

You can use the following Artisan command to generate a new enum class:

```php
php artisan make:enum UserType
```

Now, you just need to add the possible values your enum can have as constants.

```php
<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;
    const Subscriber = 2;
    const SuperAdministrator = 3;
}
```

That's it! Note that because the enum values are defined as plain constants,
you can simple access them like any other class constant.

```php
UserType::Administrator // Has a value of 0
```

### Instantiation

It can be useful to instantiate enums in order to pass them between functions
with the benefit of type hinting.

Additionally, it's impossible to instantiate an enum with an invalid value,
therefore you can be certain that the passed value is always valid.

For convenience, enums can be instantiated in multiple ways:

```php
// Standard new PHP class, passing the desired enum value as a parameter
$enumInstance = new UserType(UserType::Administrator);

// Same as the constructor, instantiate by value
$enumInstance = UserType::fromValue(UserType::Administrator);

// Use an enum key instead of its value
$enumInstance = UserType::fromKey('Administrator');

// Statically calling the key name as a method, utilizing __callStatic magic
$enumInstance = UserType::Administrator();

// Attempt to instantiate a new Enum using the given key or value. Returns null if the Enum cannot be instantiated.
$enumInstance = UserType::coerce($someValue);
```

If you want your IDE to autocomplete the static instantiation helpers, you can
generate PHPDoc annotations through an artisan command.

By default all Enums in `app/Enums` will be annotated (you can change the folder by passing a path to `--folder`)

```bash
php artisan enum:annotate
```

You can annotate a single class by specifying the class name

```bash
php artisan enum:annotate "App\Enums\UserType"
```

### Instance Properties

Once you have an enum instance, you can access the `key`, `value` and `description` as properties.

```php
$userType = UserType::fromValue(UserType::SuperAdministrator);

$userType->key; // SuperAdministrator
$userType->value; // 0
$userType->description; // Super Administrator
```

This is particularly useful if you're passing an enum instance to a blade view.

### Instance Casting

Enum instances can be cast to strings as they implement the `__toString()` magic method.  
This also means they can be echoed in blade views, for example.

```php
$userType = UserType::fromValue(UserType::SuperAdministrator);

(string) $userType // '0'
```

### Instance Equality

You can check the equality of an instance against any value by passing it to the `is` method. For convenience, there is also an `isNot` method which is the exact reverse of the `is` method.

```php
$admin = UserType::fromValue(UserType::Administrator);

$admin->is(UserType::Administrator);   // true
$admin->is($admin);                    // true
$admin->is(UserType::Administrator()); // true

$admin->is(UserType::Moderator);       // false
$admin->is(UserType::Moderator());     // false
$admin->is('random-value');            // false
```

You can also check to see if the instance's value matches against an array of possible values using the `in` method. Iterables can also be checked against.

```php
$admin = UserType::fromValue(UserType::Administrator);

$admin->in([UserType::Moderator, UserType::Administrator]);     // true
$admin->in([UserType::Moderator(), UserType::Administrator()]); // true

$admin->in([UserType::Moderator, UserType::Subscriber]);        // false
$admin->in(['random-value']);                                   // false
```

### Type Hinting

One of the benefits of enum instances is that it enables you to use type hinting, as shown below.

```php
function canPerformAction(UserType $userType)
{
    if ($userType->is(UserType::SuperAdministrator)) {
        return true;
    }

    return false;
}

$userType1 = UserType::fromValue(UserType::SuperAdministrator);
$userType2 = UserType::fromValue(UserType::Moderator);

canPerformAction($userType1); // Returns true
canPerformAction($userType2); // Returns false
```

## Flagged/Bitwise Enum

Standard enums represent a single value at a time, but flagged or bitwise enums are capable of of representing multiple values simultaneously. This makes them perfect for when you want to express multiple selections of a limited set of options. A good example of this would be user permissions where there are a limited number of possible permissions but a user can have none, some or all of them.

You can create a flagged enum using the following artisan command:

`php artisan make:enum UserPermissions --flagged`

### Defining values

When defining values you must use powers of 2, the easiest way to do this is by using the _shift left_ `<<` operator like so:

```php
final class UserPermissions extends FlaggedEnum
{
    const ReadComments      = 1 << 0;
    const WriteComments     = 1 << 1;
    const EditComments      = 1 << 2;
    const DeleteComments    = 1 << 3;
    // The next one would be `1 << 4` and so on...
}
```

### Defining shortcuts

You can use the bitwise _or_ `|` to set a shortcut value which represents a given set of values.

```php
final class UserPermissions extends FlaggedEnum
{
    const ReadComments      = 1 << 0;
    const WriteComments     = 1 << 1;
    const EditComments      = 1 << 2;
    const DeleteComments    = 1 << 3;

    // Shortcuts
    const Member = self::ReadComments | self::WriteComments; // Read and write.
    const Moderator = self::Member | self::EditComments; // All the permissions a Member has, plus Edit.
    const Admin = self::Moderator | self::DeleteComments; // All the permissions a Moderator has, plus Delete.
}
```

### Instantiating a flagged enum

There are couple of ways to instantiate a flagged enum:

```php
// Standard new PHP class, passing the desired enum values as an array of values or array of enum instances
$permissions = new UserPermissions([UserPermissions::ReadComments, UserPermissions::EditComments]);
$permissions = new UserPermissions([UserPermissions::ReadComments(), UserPermissions::EditComments()]);

// Static flags method, again passing the desired enum values as an array of values or array of enum instances
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::EditComments]);
$permissions = UserPermissions::flags([UserPermissions::ReadComments(), UserPermissions::EditComments()]);
```

[Attribute casting](#attribute-casting) works in the same way as single value enums.

### Empty flagged enums

Flagged enums can contain no value at all. Every flagged enum has a pre-defined constant of `None` which is comparable to `0`.

```php
UserPermissions::flags([])->value === UserPermissions::None; // True
```

### Flagged enum methods

In addition to the standard enum methods, there are a suite of helpful methods available on flagged enums.

Note: Anywhere where a static property is passed, you can also pass an enum instance.

#### setFlags(array $flags): Enum

Set the flags for the enum to the given array of flags.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->flags([UserPermissions::EditComments, UserPermissions::DeleteComments]); // Flags are now: EditComments, DeleteComments.
```

#### addFlag($flag): Enum

Add the given flag to the enum

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->addFlag(UserPermissions::EditComments); // Flags are now: ReadComments, EditComments.
```

#### addFlags(array $flags): Enum

Add the given flags to the enum

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->addFlags([UserPermissions::EditComments, UserPermissions::WriteComments]); // Flags are now: ReadComments, EditComments, WriteComments.
```

#### addAllFlags(): Enum

Add all flags to the enum

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments]);
$permissions->addAllFlags(); // Enum now has all flags
```

#### removeFlag($flag): Enum

Remove the given flag from the enum

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->removeFlag(UserPermissions::ReadComments); // Flags are now: WriteComments.
```

#### removeFlags(array $flags): Enum

Remove the given flags from the enum

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments, UserPermissions::EditComments]);
$permissions->removeFlags([UserPermissions::ReadComments, UserPermissions::WriteComments]); // Flags are now: EditComments.
```

#### removeAllFlags(): Enum

Remove all flags from the enum

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->removeAllFlags();
```

#### hasFlag($flag): bool

Check if the enum has the specified flag.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->hasFlag(UserPermissions::ReadComments); // True
$permissions->hasFlag(UserPermissions::EditComments); // False
```

#### hasFlags(array $flags): bool

Check if the enum has all of the specified flags.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->hasFlags([UserPermissions::ReadComments, UserPermissions::WriteComments]); // True
$permissions->hasFlags([UserPermissions::ReadComments, UserPermissions::EditComments]); // False
```

#### notHasFlag($flag): bool

Check if the enum does not have the specified flag.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->notHasFlag(UserPermissions::EditComments); // True
$permissions->notHasFlag(UserPermissions::ReadComments); // False
```

#### notHasFlags(array $flags): bool

Check if the enum doesn't have any of the specified flags.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->notHasFlags([UserPermissions::ReadComments, UserPermissions::EditComments]); // True
$permissions->notHasFlags([UserPermissions::ReadComments, UserPermissions::WriteComments]); // False
```

#### getFlags(): Enum[]

Return the flags as an array of instances.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->getFlags(); // [UserPermissions::ReadComments(), UserPermissions::WriteComments()];
```

#### hasMultipleFlags(): bool

Check if there are multiple flags set on the enum.

```php
$permissions = UserPermissions::flags([UserPermissions::ReadComments, UserPermissions::WriteComments]);
$permissions->hasMultipleFlags(); // True;
$permissions->removeFlag(UserPermissions::ReadComments)->hasMultipleFlags(); // False
```

#### getBitmask(): int

Get the bitmask for the enum.

```php
UserPermissions::Member()->getBitmask(); // 11;
UserPermissions::Moderator()->getBitmask(); // 111;
UserPermissions::Admin()->getBitmask(); // 1111;
UserPermissions::DeleteComments()->getBitmask(); // 1000;
```

### Flagged enums in Eloquent queries

To use flagged enums directly in your Eloquent queries, you may use the `QueriesFlaggedEnums` trait on your model which provides you with the following methods:

#### hasFlag($column, $flag): Builder

```php
User::hasFlag('permissions', UserPermissions::DeleteComments())->get();
```

#### notHasFlag($column, $flag): Builder

```php
User::notHasFlag('permissions', UserPermissions::DeleteComments())->get();
```

#### hasAllFlags($column, $flags): Builder

```php
User::hasAllFlags('permissions', [UserPermissions::EditComment(), UserPermissions::ReadComment()])->get();
```

#### hasAnyFlags($column, $flags): Builder

```php
User::hasAnyFlags('permissions', [UserPermissions::DeleteComments(), UserPermissions::EditComments()])->get();
```

## Attribute Casting

You may cast model attributes to enums using Laravel 7.x's built in custom casting. This will cast the attribute to an enum instance when getting and back to the enum value when setting.
Since `Enum::class` implements the `Castable` contract, you just need to specify the classname of the enum:

```php
use BenSampo\Enum\Tests\Enums\UserType;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    protected $casts = [
        'random_flag' => 'boolean',     // Example standard laravel cast
        'user_type' => UserType::class, // Example enum cast
    ];
}
```

Now, when you access the `user_type` attribute of your `Example` model,
the underlying value will be returned as a `UserType` enum.

```php
$example = Example::first();
$example->user_type // Instance of UserType
```

Review the [methods and properties available on enum instances](#instantiation) to get the most out of attribute casting.

You can set the value by either passing the enum value or another enum instance.

```php
$example = Example::first();

// Set using enum value
$example->user_type = UserType::Moderator;

// Set using enum instance
$example->user_type = UserType::Moderator();
```

### Customising `$model->toArray()` behaviour

When using `toArray` (or returning model/models from your controller as a response) Laravel will call the `toArray` method on the enum instance.

By default, this will return only the value in its native type. You may want to also have access to the other properties (key, description), for example to return
to javascript app.

To customise this behaviour, you can override the `toArray` method on the enum instance.

```php
// Example Enum
final class UserType extends Enum
{
    const ADMINISTRATOR = 0;
    const MODERATOR = 1;
}

$instance = UserType::Moderator();

// Default
public function toArray()
{
    return $this->value;
}
// Returns int(1)

// Return all properties
public function toArray()
{
    return $this;
}
// Returns an array of all the properties
// array(3) {
//  ["value"]=>
//  int(1)"
//  ["key"]=>
//  string(9) "MODERATOR"
//  ["description"]=>
//  string(9) "Moderator"
// }

```

### Casting underlying native types

Many databases return everything as strings (for example, an integer may be returned as the string `'1'`).
To reduce friction for users of the library, we use type coercion to figure out the intended value. If you'd prefer to control this, you can override the `parseDatabase` static method on your enum class:

```php
final class UserType extends Enum
{
    const Administrator = 0;
    const Moderator = 1;

    public static function parseDatabase($value)
    {
        return (int) $value;
    }
}
```

Returning `null` from the `parseDatabase` method will cause the attribute on the model to also be `null`. This can be useful if your database stores inconsistent blank values such as empty strings instead of `NULL`.

### Model Annotation

If you're using Laravel 7 casting, the [laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) package can be used to automatically generate property docblocks for your models.

## Migrations

### Recommended

Because enums enforce consistency at the code level it's not necessary to do so again at the database level, therefore the recommended type for database columns is `string` or `int` depending on your enum values. This means you can add/remove enum values in your code without worrying about your database layer.

```php
use App\Enums\UserType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('type')
                ->default(UserType::Moderator);
        });
    }
}
```

### Using `enum` column type

Alternatively you may use `Enum` classes in your migrations to define enum columns.
The enum values must be defined as strings.

```php
use App\Enums\UserType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->enum('type', UserType::getValues())
                ->default(UserType::Moderator);
        });
    }
}
```

## Validation

### Array Validation

#### Enum value

You may validate that an enum value passed to a controller is a valid value for a given enum by using the `EnumValue` rule.

```php
use BenSampo\Enum\Rules\EnumValue;

public function store(Request $request)
{
    $this->validate($request, [
        'user_type' => ['required', new EnumValue(UserType::class)],
    ]);
}
```

By default, type checking is set to strict, but you can bypass this by passing `false` to the optional second parameter of the EnumValue class.

```php
new EnumValue(UserType::class, false) // Turn off strict type checking.
```

#### Enum key

You can also validate on keys using the `EnumKey` rule. This is useful if you're taking the enum key as a URL parameter for sorting or filtering for example.

```php
use BenSampo\Enum\Rules\EnumKey;

public function store(Request $request)
{
    $this->validate($request, [
        'user_type' => ['required', new EnumKey(UserType::class)],
    ]);
}
```

#### Enum instance

Additionally you can validate that a parameter is an instance of a given enum.

```php
use BenSampo\Enum\Rules\Enum;

public function store(Request $request)
{
    $this->validate($request, [
        'user_type' => ['required', new Enum(UserType::class)],
    ]);
}
```

### Pipe Validation

You can also use the 'pipe' syntax for rules.

**enum_value**_:enum_class,[strict]_  
**enum_key**_:enum_class_  
**enum**_:enum_class_

```php
'user_type' => 'required|enum_value:' . UserType::class,
'user_type' => 'required|enum_key:' . UserType::class,
'user_type' => 'required|enum:' . UserType::class,
```

## Localization

### Validation messages

Run the following command to publish the language files to your `resources/lang` folder.

```
php artisan vendor:publish --provider="BenSampo\Enum\EnumServiceProvider" --tag="translations"
```

### Enum descriptions

You can translate the strings returned by the `getDescription` method using Laravel's built in [localization](https://laravel.com/docs/5.6/localization) features.

Add a new `enums.php` keys file for each of your supported languages. In this example there is one for English and one for Spanish.

```php
// resources/lang/en/enums.php
<?php

use App\Enums\UserType;

return [

    UserType::class => [
        UserType::Administrator => 'Administrator',
        UserType::SuperAdministrator => 'Super administrator',
    ],

];
```

```php
// resources/lang/es/enums.php
<?php

use App\Enums\UserType;

return [

    UserType::class => [
        UserType::Administrator => 'Administrador',
        UserType::SuperAdministrator => 'Súper administrador',
    ],

];
```

Now, you just need to make sure that your enum implements the `LocalizedEnum` interface as demonstrated below:

```php
use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class UserType extends Enum implements LocalizedEnum
{
    // ...
}
```

The `getDescription` method will now look for the value in your localization files. If a value doesn't exist for a given key, the default description is returned instead.

## Overriding the getDescription method

If you'd like to return a custom value from the getDescription method, you may do so by overriding the method on your enum:

```php
public static function getDescription($value): string
{
    if ($value === self::SuperAdministrator) {
        return 'Super admin';
    }

    return parent::getDescription($value);
}
```

Calling `UserType::getDescription(3);` now returns `Super admin` instead of `Super administator`.

## Extending the Enum Base Class

The `Enum` base class implements the [Laravel `Macroable`](https://laravel.com/api/5.6/Illuminate/Support/Traits/Macroable.html) trait, meaning it's easy to extend it with your own functions. If you have a function that you often add to each of your enums, you can use a macro.

Let's say we want to be able to get a flipped version of the enum `asArray` method, we can do this using:

```php
Enum::macro('asFlippedArray', function() {
    return array_flip(self::asArray());
});
```

Now, on each of my enums, I can call it using `UserType::asFlippedArray()`.

It's best to register the macro inside of a service providers' boot method.

## Laravel Nova Integration

Use the [nova-enum-field](https://github.com/simplesquid/nova-enum-field) package by Simple Squid to easily create fields for your Enums in Nova. See their readme for usage.

## PHPStan integration

If you are using [PHPStan](https://github.com/phpstan/phpstan) for static
analysis, you can enable the extension for proper recognition of the
magic instantiation methods.

Add the following to your projects `phpstan.neon` includes:

```neon
includes:
- vendor/bensampo/laravel-enum/extension.neon
```

## Artisan Command List

`php artisan make:enum`  
Create a new enum class. Pass `--flagged` as an option to create a flagged enum.  
[Find out more](#enum-definition)

`php artisan enum:annotate`  
Generate DocBlock annotations for enum classes.  
[Find out more](#instantiation)

## Enum Class Reference

### static getKeys(): array

Returns an array of the keys for an enum.

```php
UserType::getKeys(); // Returns ['Administrator', 'Moderator', 'Subscriber', 'SuperAdministrator']
```

### static getValues(): array

Returns an array of the values for an enum.

```php
UserType::getValues(); // Returns [0, 1, 2, 3]
```

### static getKey(mixed $value): string

Returns the key for the given enum value.

```php
UserType::getKey(1); // Returns 'Moderator'
UserType::getKey(UserType::Moderator); // Returns 'Moderator'
```

### static getValue(string $key): mixed

Returns the value for the given enum key.

```php
UserType::getValue('Moderator'); // Returns 1
```

### static hasKey(string $key): bool

Check if the enum contains a given key.

```php
UserType::hasKey('Moderator'); // Returns 'True'
```

### static hasValue(mixed $value, bool $strict = true): bool

Check if the enum contains a given value.

```php
UserType::hasValue(1); // Returns 'True'

// It's possible to disable the strict type checking:
UserType::hasValue('1'); // Returns 'False'
UserType::hasValue('1', false); // Returns 'True'
```

### static getDescription(mixed $value): string

Returns the key in sentence case for the enum value. It's possible to [override the getDescription](#overriding-the-getDescription-method) method to return custom descriptions.

```php
UserType::getDescription(3); // Returns 'Super administrator'
UserType::getDescription(UserType::SuperAdministrator); // Returns 'Super administrator'
```

### static getRandomKey(): string

Returns a random key from the enum. Useful for factories.

```php
UserType::getRandomKey(); // Returns 'Administrator', 'Moderator', 'Subscriber' or 'SuperAdministrator'
```

### static getRandomValue(): mixed

Returns a random value from the enum. Useful for factories.

```php
UserType::getRandomValue(); // Returns 0, 1, 2 or 3
```

### static getRandomInstance(): mixed

Returns a random instance of the enum. Useful for factories.

```php
UserType::getRandomInstance(); // Returns an instance of UserType with a random value
```

### static asArray(): array

Returns the enum key value pairs as an associative array.

```php
UserType::asArray(); // Returns ['Administrator' => 0, 'Moderator' => 1, 'Subscriber' => 2, 'SuperAdministrator' => 3]
```

### static asSelectArray(): array

Returns the enum for use in a select as value => description.

```php
UserType::asSelectArray(); // Returns [0 => 'Administrator', 1 => 'Moderator', 2 => 'Subscriber', 3 => 'Super administrator']
```

### static fromValue(mixed $enumValue): Enum

Returns an instance of the called enum. Read more about [enum instantiation](#instantiation).

```php
UserType::fromValue(UserType::Administrator); // Returns instance of Enum with the value set to UserType::Administrator
```

### static getInstances(): array

Returns an array of all possible instances of the called enum, keyed by the constant names.

```php
var_dump(UserType::getInstances());

array(4) {
  'Administrator' =>
  class BenSampo\Enum\Tests\Enums\UserType#415 (3) {
    public $key =>
    string(13) "Administrator"
    public $value =>
    int(0)
    public $description =>
    string(13) "Administrator"
  }
  'Moderator' =>
  class BenSampo\Enum\Tests\Enums\UserType#396 (3) {
    public $key =>
    string(9) "Moderator"
    public $value =>
    int(1)
    public $description =>
    string(9) "Moderator"
  }
  'Subscriber' =>
  class BenSampo\Enum\Tests\Enums\UserType#393 (3) {
    public $key =>
    string(10) "Subscriber"
    public $value =>
    int(2)
    public $description =>
    string(10) "Subscriber"
  }
  'SuperAdministrator' =>
  class BenSampo\Enum\Tests\Enums\UserType#102 (3) {
    public $key =>
    string(18) "SuperAdministrator"
    public $value =>
    int(3)
    public $description =>
    string(19) "Super administrator"
  }
}
```

### static coerce(mixed $enumKeyOrValue): ?Enum

Attempt to instantiate a new Enum using the given key or value. Returns null if the Enum cannot be instantiated.

```php
UserType::coerce(0); // Returns instance of UserType with the value set to UserType::Administrator
UserType::coerce('Administrator'); // Returns instance of UserType with the value set to UserType::Administrator
UserType::coerce(99); // Returns null (not a valid enum value)
```

## Stubs

Run the following command to publish the stub files to the `stubs` folder in the root of your application.

```shell
php artisan vendor:publish --provider="BenSampo\Enum\EnumServiceProvider" --tag="stubs"
```
