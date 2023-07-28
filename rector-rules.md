# 2 Rules Overview

## ToNativeImplementationRector

Convert usages of `BenSampo\Enum\Enum` to native PHP enums

:wrench: **configure it!**

- class: [`BenSampo\Enum\Rector\ToNativeImplementationRector`](src/Rector/ToNativeImplementationRector.php)

```php
<?php

declare(strict_types=1);

use BenSampo\Enum\Rector\ToNativeImplementationRector;
use BenSampo\Enum\Tests\Enums\UserType;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ToNativeImplementationRector::class, [
        UserType::class,
    ]);
};
```

↓

```diff
-/**
- * @method static static ADMIN()
- * @method static static MEMBER()
- *
- * @extends Enum<int>
- */
-class UserType extends Enum
+enum UserType : int
 {
-    const ADMIN = 1;
-    const MEMBER = 2;
+    case ADMIN = 1;
+    case MEMBER = 2;
 }
```

<br>

## ToNativeUsagesRector

Convert usages of `BenSampo\Enum\Enum` to native PHP enums

:wrench: **configure it!**

- class: [`BenSampo\Enum\Rector\ToNativeUsagesRector`](src/Rector/ToNativeUsagesRector.php)

```php
<?php

declare(strict_types=1);

use BenSampo\Enum\Rector\ToNativeUsagesRector;
use BenSampo\Enum\Tests\Enums\UserType;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ToNativeUsagesRector::class, [
        UserType::class,
    ]);
};
```

↓

```diff
-$user = UserType::ADMIN();
-$user->is(UserType::ADMIN);
+$user = UserType::ADMIN;
+$user === UserType::ADMIN;
```

<br>
