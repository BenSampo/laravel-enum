# Upgrade Guide

## 6.x

### Native types

The library now uses native types whenever possible.
When you override methods or implement interfaces, you will need to add them.

### `Enum::getDescription()` throws

Instead of returning an empty string `''` on invalid values,
`Enum::getDescription()` will throw an `InvalidEnumMemberException`.

### Construct `InvalidEnumMemberException`

The constructor of `InvalidEnumMemberException` now expects the class name
of an enum instead of an enum instance.

## 5.x

### Laravel 9 required

Laravel `9` or higher is required.

### PHP 8.0 required

PHP `8.0` or higher is now required.

## 4.x

### Review use of Localization features

You should make sure that any enums using localization features are still translated as expected.

## 3.x

### Laravel 8 required

Laravel `8` or higher is required.

### PHP 7.3 required

PHP `7.3` or higher is now required.

## 2.x

### Laravel 7.5 required

Laravel `7.5` or higher is required for the new native attribute casting.

### PHP 7.2 required

PHP `7.2.5` or higher is now required.

### Switch to native casting

You should update your models to use Laravel 7 native casting. Remove the trait and
move the casts from `$enumCasts` to `$casts`.

Trait based casting is still present, but is now deprecated and will be removed in the next major version.

```diff
--use BenSampo\Enum\Traits\CastsEnums;

class MyModel extends Model
{
-   use CastsEnums;

-   protected $enumCasts = [
+   protected $casts = [
        'foo' => Foo::class,
    ];
```
