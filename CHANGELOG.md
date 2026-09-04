# Changelog

All notable changes to `laravel-system-log` will be documented in this file.

## v1.5 Avoid MySQL insertion errors on very long 'message' values - 2026-09-04

Make sure the message is less than MySQL's maximum for a text field to avoid insertion errors on huge 'message' (e.g. from exceptions being logged with their traces).

## v1.4 Add Laravel 13 Support - 2026-05-20

### What's Changed

* Laravel 13 Support by @Patabugen in https://github.com/steadfast-collective/laravel-system-log/pull/15

**Full Changelog**: https://github.com/steadfast-collective/laravel-system-log/compare/v1.3...v1.4

## v1.3 Pass SystemLoggableContract to addSystemLog - 2026-03-04

### What's Changed

- Allow passing any `SystemLoggableContract` as the `model` parameter to HasSystemLogger::addSystemLog

Previously `addSystemLog` only accepted an Eloquent Model as the `model` parameter, but now anything which implements the methods required by the `SystemLoggableContract` can be passed.

Those methods are:

- public function getInternalId(): ?string;
- public function getInternalType(): string;
- public function getExternalId(): ?string;
- public function getExternalType(): string;

## v1.2 - 2026-01-07

- Added `code` field to SystemLog

## v1.1 - 2025-10-21

- Various fixes and improvements after trying the package out in real projects.

## v1.0 - 2025-10-20

Initial release with:

- Model & Factory
- Filament Resources
- Fields but not logic for Retrying
