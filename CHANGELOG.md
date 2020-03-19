# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/BenSampo/laravel-enum/compare/v1.32...master)

### Added

- Added translations

### Changed

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
