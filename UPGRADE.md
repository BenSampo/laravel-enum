# Upgrade Guide

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
