# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## 6.7.0

### Added

- Add PHPStan rule to detect duplicate enum values

## 6.6.4

### Fixed

- Fix conversion of `Enum::fromKey()` to native enum

## 6.6.3

### Fixed

- Remove leading backslash in class names passed to `php artisan enum:to-native`

## 6.6.2

### Fixed

- Convert single classes in one step with `php artisan enum:to-native`

## 6.6.1

### Fixed

- Disable timeout of rector calls in `php artisan enum:to-native`

## 6.6.0

### Changed

- Use command `enum:to-native` for simplified one-step conversion of classes that extend `BenSampo\Enum\Enum` to native PHP enums

## 6.5.0

### Added

- Add Rector rules for conversion of classes that extend `BenSampo\Enum\Enum` to native PHP enums

### Deprecated

- Deprecate command `enum:to-native` in favor of Rector conversion

## 6.4.1

### Fixed

- Ensure validation rules are always added https://github.com/BenSampo/laravel-enum/pull/327

## 6.4.0

### Added

- Add command `enum:to-native` to convert a class that extends `BenSampo\Enum\Enum` to a native PHP enum

### Fixed

- Load pipe-string syntax validation translations lazily https://github.com/BenSampo/laravel-enum/pull/324

## 6.3.3

### Fixed

- Allow `mixed` in `Enum::hasValue()`

## 6.3.2

### Fixed

- Preserve whitespace in PHPDocs when running `enum:annotate` command

## 6.3.1

### Fixed

- Mark `Enum::$key` and `Enum::$description` as non-nullable in `Enum` and document they are unset in `FlaggedEnum`

## [6.3.0](https://github.com/BenSampo/laravel-enum/compare/v6.2.2...v6.3.0) - 2023-01-31

### Added

- Support Laravel 10 [298](https://github.com/BenSampo/laravel-enum/pull/298)

## [6.2.2](https://github.com/BenSampo/laravel-enum/compare/v6.2.1...v6.2.2) - 2023-01-17

### Fixed

- Fix backtrack regexp error and add Windows EOL support to the annotate command [296](https://github.com/BenSampo/laravel-enum/pull/296)

## [6.2.1](https://github.com/BenSampo/laravel-enum/compare/v6.2.0...v6.2.1) - 2023-01-12

### Fixed

- Fix running `php artisan enum:annotate` on long enum class [294](https://github.com/BenSampo/laravel-enum/pull/294)

## [6.2.0](https://github.com/BenSampo/laravel-enum/compare/v6.1.0...v6.2.0) - 2022-12-07

### Changed

- Open `EnumServiceProvider` for customization [292](https://github.com/BenSampo/laravel-enum/pull/292)

## [6.1.0](https://github.com/BenSampo/laravel-enum/compare/v6.0.0...v6.1.0) - 2022-10-26

### Changed

- Eliminate unnecessary abstract class `AbstractAnnotationCommand` [283](https://github.com/BenSampo/laravel-enum/pull/283)

### Fixed

- Provide more accurate type hints in `Enum` and `FlaggedEnum` [283](https://github.com/BenSampo/laravel-enum/pull/283)
- Accept `FlaggedEnum` instances in `QueriesFlaggedEnums` scopes [283](https://github.com/BenSampo/laravel-enum/pull/283)

## [6.0.0](https://github.com/BenSampo/laravel-enum/compare/v5.3.1...v6.0.0) - 2022-08-22

### Added

- Allow Description attribute usage on class [270](https://github.com/BenSampo/laravel-enum/pull/270)
- Add generic type `TValue` to `Enum` class

### Changed

- Require composer/class-map-generator over composer/composer [268](https://github.com/BenSampo/laravel-enum/pull/268)
- Use native types whenever possible
- Throw when calling `Enum::getDescription()` with invalid values
- Expect class-string in `InvalidEnumMemberException` constructor

### Fixed

- Leverage late static binding for instantiation methods in PHPStan extension

### Removed

- Remove `Enum::getInstance()` in favor or `Enum::fromValue()`

## [5.3.1](https://github.com/BenSampo/laravel-enum/compare/v5.3.0...v5.3.1) - 2022-06-22

### Fixed

- Narrow property type hints [258](https://github.com/BenSampo/laravel-enum/pull/258)

## [5.3.0](https://github.com/BenSampo/laravel-enum/compare/v5.2.0...v5.3.0) - 2022-04-08

### Fixed

- Return value for invalid enum case when using the `Description` attribute [264](https://github.com/BenSampo/laravel-enum/pull/264)

### Fixed

- Type-hint `Enum::$key` and `Enum::$description` as `string`
- Type-hint `FlaggedEnum::$value` as `int`

## [5.2.0](https://github.com/BenSampo/laravel-enum/compare/v5.1.0...v5.2.0) - 2022-03-11

### Fixed

- Publish language definitions to `lang` directory [254](https://github.com/BenSampo/laravel-enum/pull/254)

### Added

- Restore enum instance from `var_export()` [252](https://github.com/BenSampo/laravel-enum/pull/252)

## [5.1.0](https://github.com/BenSampo/laravel-enum/compare/v5.0.0...v5.1.0) - 2022-02-09

### Added

- Ability to define enum case descriptions using `Description` attribute.

## [5.0.0](https://github.com/BenSampo/laravel-enum/compare/v4.2.0...v5.0.0) - 2022-02-09

### Added

- Support for Laravel 9

### Changed

- The `annotate` command now uses composer to parse directories for instances of enums instead of `hanneskod/classtools`

### Removed

- Removed old `CastsEnums` trait. Laravel attribute casting should be used now instead. [247](https://github.com/BenSampo/laravel-enum/pull/247)

## [4.2.0](https://github.com/BenSampo/laravel-enum/compare/v4.1.0...v4.2.0) - 2022-01-31

### Fixed

- Fix return type on FlaggedEnum flags method [241](https://github.com/BenSampo/laravel-enum/pull/241)
- Suppress deprecated notice on PHP8.1 [236](https://github.com/BenSampo/laravel-enum/pull/236)

## [4.1.0](https://github.com/BenSampo/laravel-enum/compare/v4.0.0...v4.1.0) - 2021-11-16

### Added

- Allow package to be installed with PHP 8.1 [#233](https://github.com/BenSampo/laravel-enum/pull/233)

### Changed

- Allow `laminas/laminas-code:^4.0` as a dependency [#233](https://github.com/BenSampo/laravel-enum/pull/233)

## [4.0.0](https://github.com/BenSampo/laravel-enum/compare/v3.4.2...v4.0.0) - 2021-11-09

### Fixed

- Fixed validation error message localization when using string validation rules [#227](https://github.com/BenSampo/laravel-enum/pull/227)

### Changed

- Extend the functionality of the `getKeys()` and `getValues()` methods [#223](https://github.com/BenSampo/laravel-enum/pull/223)

### Added

- Added new method `notIn()` to check whether a value is not in an iterable set of values [#232](https://github.com/BenSampo/laravel-enum/pull/232)

## [3.4.2](https://github.com/BenSampo/laravel-enum/compare/v3.4.1...v3.4.2) - 2021-09-09

### Fixed

- Fixed broken enums due to wrapping of long constant names in method annotations [#226](https://github.com/BenSampo/laravel-enum/pull/226)

## [3.4.1](https://github.com/BenSampo/laravel-enum/compare/v3.4.0...v3.4.1) - 2021-06-17

### Fixed

- Fixed type issued in PHP 7.3

## [3.4.0](https://github.com/BenSampo/laravel-enum/compare/v3.3.0...v3.4.0) - 2021-06-17

### Added

- `addAllFlags()` method to flagged enums
- `removeAllFlags()` method to flagged enums

### Fixed

- Fixed coercion of flagged enums when the value represents multiple flags

## [3.3.0](https://github.com/BenSampo/laravel-enum/compare/v3.2.0...v3.3.0) - 2021-02-16

### Changed

- Update doctrine/dbal requirement from ^2.9 to ^2.9|^3.0 [#208](https://github.com/BenSampo/laravel-enum/pull/208)
- Allow passing iterables to Enum::in() [#212](https://github.com/BenSampo/laravel-enum/pull/212)

### Fixed

- fix: `$model->getChanges()` triggered due to strict comparison [#187](https://github.com/BenSampo/laravel-enum/pull/187)
- Fixed issue in `getFriendlyKeyName`when uppercase key contains non-alpha characters [#210](https://github.com/BenSampo/laravel-enum/pull/210)

## [3.2.0](https://github.com/BenSampo/laravel-enum/compare/v3.1.0...v3.2.0) - 2020-12-15

### Added

- PHP 8.0 support [#203](https://github.com/BenSampo/laravel-enum/pull/203)

### Changed

- Switched from Travis to GitHub Actions

## [3.1.0](https://github.com/BenSampo/laravel-enum/compare/v3.0.0...v3.1.0) - 2020-10-22

### Added

- Added trait to query flagged enums using Eloquent [#180](https://github.com/BenSampo/laravel-enum/pull/180)
- Add the option to publish enums stubs [#182](https://github.com/BenSampo/laravel-enum/pull/182)

### Changed

- Improved test equality strictness [#185](https://github.com/BenSampo/laravel-enum/pull/185)

### Fixed

- fix:`toSelectArray` breaking change + document `toArray` change [#184](https://github.com/BenSampo/laravel-enum/pull/184)

## [3.0.0](https://github.com/BenSampo/laravel-enum/compare/v2.2.0...v3.0.0) - 2020-08-07

### Added

- Support for Laravel 8

### Fixed

- Model annotation has been removed in favour of `laravel-ide-helper` [#165](https://github.com/BenSampo/laravel-enum/pull/165)

## [2.2.0](https://github.com/BenSampo/laravel-enum/compare/v2.1.0...v2.2.0) - 2020-08-30

### Fixed

- Model attributes which use Laravel 7 native casting now return the enum value when serialized. [#162](https://github.com/BenSampo/laravel-enum/issues/162) [#163](https://github.com/BenSampo/laravel-enum/issues/163)

### Deprecated

- `Enum::toArray()` should no longer be called statically, instead use `Enum::asArray()`.

## [2.1.0](https://github.com/BenSampo/laravel-enum/compare/v2.0.0...v2.1.0) - 2020-07-24

### Fixed

- Allow returning `null` when using native casting [#152](https://github.com/BenSampo/laravel-enum/pull/152)

## [2.0.0](https://github.com/BenSampo/laravel-enum/compare/v1.38.0...v2.0.0) - 2020-07-02

### Added

- Native attribute casting [#131](https://github.com/BenSampo/laravel-enum/pull/131)

### Changed

- Require Laravel 7.5 or higher
- Require PHP 7.2.5 or higher

### Deprecated

- Deprecate legacy attribute casting in favor of native casting

## [1.38.0](https://github.com/BenSampo/laravel-enum/compare/v1.37.0...v1.38.0) - 2020-06-07

### Fixed

- Handle calling magic instantiation methods from within instance methods of the Enum [#147](https://github.com/BenSampo/laravel-enum/pull/147)
- Add new instantiation methods `Enum::fromKey()` and `Enum::fromValue()` [#142](https://github.com/BenSampo/laravel-enum/pull/142)
- Fixed issue with localized validation messages [#141](https://github.com/BenSampo/laravel-enum/pull/141)

### Deprecated

- Deprecate `Enum::getInstance()` in favor of `Enum::fromValue()`

## [1.37.0](https://github.com/BenSampo/laravel-enum/compare/v1.36.0...v1.37.0) - 2020-04-11

### Changed

- EnumValue validation rule allows multiple flags for FlaggedEnums

## [1.36.0](https://github.com/BenSampo/laravel-enum/compare/v1.35...v1.36.0) - 2020-03-22

### Changed

- Validation messages are now pulled from translation files [#134](https://github.com/BenSampo/laravel-enum/pull/134)

## [1.35.0](https://github.com/BenSampo/laravel-enum/compare/v1.34...v1.35) - 2020-03-16

### Changed

- Added missing pipe validation syntax for enum instance validation [#132](https://github.com/BenSampo/laravel-enum/pull/132)

## [1.34.0](https://github.com/BenSampo/laravel-enum/compare/v1.33...v1.34) - 2020-03-13

### Changed

- Change order of attributes in `BenSampo\Enum\Enum`, to ensure relational comparison (with <,>) uses the $value attribute. (Ref: https://www.php.net/manual/en/language.oop5.object-comparison.php#98725) [#129](https://github.com/BenSampo/laravel-enum/pull/129)
- Fix for Lumen when Facade not set [#123](https://github.com/BenSampo/laravel-enum/pull/123)

## [1.33.0](https://github.com/BenSampo/laravel-enum/compare/v1.32...v1.33) - 2020-03-05

### Added

- Add Laravel 7.x compatibility

## [1.32.0](https://github.com/BenSampo/laravel-enum/compare/v1.31...v1.32) - 2020-02-11

### Added

- Add tests and make `EnumMethodReflection` return generating constant values for `isInternal`, `isDeprecated`, and
  `getDeprecatedDescription` [#121](https://github.com/BenSampo/laravel-enum/pull/121)

## [1.31.0](https://github.com/BenSampo/laravel-enum/compare/v1.30...v1.31) - 2020-02-09

### Added

- Add compatibility with PHPStan `0.12.x` [#119](https://github.com/BenSampo/laravel-enum/pull/119)
- Changelog started.
