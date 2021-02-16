# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/BenSampo/laravel-enum/compare/v3.1.0...master)

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
