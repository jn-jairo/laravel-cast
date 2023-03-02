# Changelog

## [Unreleased](https://github.com/jn-jairo/laravel-cast/compare/v2.0.0...2.x)

## [v2.0.0 (2023-03-02)](https://github.com/jn-jairo/laravel-cast/compare/v1.0.6...v2.0.0)

### Added
- Laravel 10 support
- `compressed` type
- `base64` type
- `pipe` type
- Static analysis
- Mutation testing

### Changed
- `encrypted` type usage
- `\JnJairo\Laravel\Cast\Contracts\Type` contract now has a `setCast` and `getCast` method to access the `Cast` instance
- Minimal PHP version 8.1
- Minimal Laravel version 8.83
- Tests using Pest
- Code style PSR-12
