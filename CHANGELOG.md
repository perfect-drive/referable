# Changelog

All notable changes to `referable` will be documented in this file.

## 2.2.0 - 2026-07-22

- `ReferableEnum::getReferenceValue()` now auto-detects `label()` for enums that expose no `name()` method. `name()` still takes precedence whenever it is defined, so every enum that works as referable today is unaffected — the change is strictly backwards-compatible. Consumers that have moved to a `label()`-only convention (Filament / Nova) no longer need a per-enum `getReferenceValue` override and avoid shadowing PHP's built-in `name` property. To switch an existing enum from `name()` to `label()`, do it explicitly (remove `name()` or override `getReferenceValue()`).
- `ReferableFinder::all()` is now resilient to per-class autoload failures. A single broken class (syntax error, missing parent, etc.) no longer aborts the entire route-registration pass; the offending class is skipped and the rest of the project still gets its referable routes. Skipped classes are reported via `error_log()` so the issue surfaces in the web-server error log instead of disappearing silently.
- The sister `ReferableModel` trait still hardcodes `'name'` and is unchanged. The `label()` auto-detect is enum-only for now because the underlying motivation (PHP enums' built-in `name` property colliding with a custom `name()` method) doesn't apply to Eloquent models. Open to mirroring the auto-detect for models if there's demand.

## 2.1.0 - 2026-07-14

- Model scopes marked with `#[ReferableScope]` can now be defined using Laravel's native `#[Scope]` attribute in addition to the legacy `scopeActive` naming convention. Scopes are resolved through the model's `hasNamedScope()`, so both notations are fully supported and existing `scopeActive`-style scopes keep working unchanged.

## 2.0.1 - 2026-04-23

- Fixed CI workflows (pint auto-commit and PHPStan PHP version)
- Applied pint code style fixes
- Narrowed types in `ReferableServiceProvider` and `ReferableFinder` to satisfy PHPStan 2

## 2.0.0 - 2026-04-22

- Laravel 13 support
- Dropped support for Laravel 10 and Laravel 11
- Dropped support for PHP 8.2 (minimum PHP version is now 8.3)

## 1.3 - 2025-03-03

Laravel 12 support

## 1.2 - 2024-06-14

Laravel 11 support

## 1.1.0

- Added scoping for Enums
- Added possibility to use middleware for the Referable routes
- Fixed some issues with booting the package and publishing the config file
- Added tests

## 1.0.0

- initial release
