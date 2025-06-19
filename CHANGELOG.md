# Craft Ignition Changelog

All notable changes to this project will be documented in this file.

## 1.0.7 - 2025-06-19

- Add svg icon.

## 1.0.6 - 2025-06-19

- Updates to comply plugin store requirements.

## 1.0.5 - 2025-06-19

- Update `composer.json` to require `webrgp/craft-ignition-core` version `^1.1.0`.

## 1.0.4 - 2025-01-26

- Fix missing error action template on 404 errors. #13

## 1.0.3 - 2025-01-03

- Improve type hinting and code formatting everywhere.
- Fix PHPStan issues.
- Update `composer.json` with support links.

## 1.0.2 - 2025-01-02

- Fix Craft 4 compatibility issue on `AddCraftInfo` middleware.

## 1.0.1.1 - 2025-01-02

- Enhance code formatting by removing unused middleware and adjusting function syntax in the IgnitionRenderer.

## 1.0.1 - 2025-01-02

- Refactor the `IgnitionRenderer` for improved code clarity and remove unused middleware.
- Update the `README.md` to provide better documentation on the IgnitionErrorHandler and middleware functionality.

## 1.0.0 - 2025-01-02

- Initial stable release
- Enhance Ignition module with configuration and middleware improvements by @webrgp

## 1.0.0-beta-1 - 2024-12-25

- Add `src/web/views/exception.php` view file, to render the exception page
- Update `src/web/IgnitionErrorHandler.php` to use the new view file.

## 1.0.0-beta - 2024-12-05

- Add `LICENSE` file
- Updated installation instructions in `README.md`
- Ignore `composer.lock` file

## 1.0.0-alpha - 2024-11-20

- Initial alpha release
