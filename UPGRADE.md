# Upgrade Guide

## 2.x

### Laravel 7 Required
`2.x` uses new features not present in earlier laravel versions, so Laravel 7 is required.
PHP `7.2.5` or greater is also now required.

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
