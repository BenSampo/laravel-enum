# 2 Rules Overview

## ToNativeImplementationRector

Convert usages of `BenSampo\Enum\Enum` to native PHP enums

:wrench: **configure it!**

- class: [`BenSampo\Enum\Rector\ToNativeImplementationRector`](src/Rector/ToNativeImplementationRector.php)

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

```diff
-$user = UserType::ADMIN();
-$user->is(UserType::ADMIN);
+$user = UserType::ADMIN;
+$user === UserType::ADMIN;
```

<br>
