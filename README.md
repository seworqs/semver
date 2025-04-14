# SEworqs SemVer 

A type-safe, chainable semantic versioning implementation for PHP with support for bumping versions, handling pre-releases, and comparing versions.

## Installation

Install via Composer.
```bash
$> composer require seworqs/semver
```

## Usage
```php
use Seworqs\Semver;
use Seworqs\Semver\Enum\EnumBumpReleaseType;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;

// Create a semver from a string.
$semver = Semver::fromString('2.5.3');

// Bumping will give you a new Semver!
$newSemver = $semver->bump(EnumBumpReleaseType::PATCH);

// $version = '2.5.4'
$version = $newSemver->getCurrentVersion();
```
> [More examples](docs/Examples.md)

## Features 
- [X] Bump to a release version
- [X] Bump to a pre-release version
- [X] Bump to a release version and start with a pre-release
- [X] Compare versions

> See our [examples](docs/Examples.md)
 
## Classes and namespaces

| Namespace      | Class  |
|----------------|--------|
| Seworqs\Semver | Semver |

## License

Apache-2.0, see [LICENSE](./LICENSE)

## About SEworqs
Seworqs builds clean, reusable modules for PHP and Mendix developers.

Learn more at [github.com/seworqs](https://github.com/seworqs)

## Badges
[![Latest Version](https://img.shields.io/packagist/v/seworqs/semver.svg?style=flat-square)](https://packagist.org/packages/seworqs/semver)
[![Total Downloads](https://img.shields.io/packagist/dt/seworqs/semver.svg?style=flat-square)](https://packagist.org/packages/seworqs/semver)
[![License](https://img.shields.io/packagist/l/seworqs/semver?style=flat-square)](https://packagist.org/packages/seworqs/semver)
[![PHP Version](https://img.shields.io/packagist/php-v/seworqs/semver.svg?style=flat-square)](https://packagist.org/packages/seworqs/semver)
[![Made by SEworqs](https://img.shields.io/badge/made%20by-SEworqs-002d74?style=flat-square&logo=https://raw.githubusercontent.com/seworqs/semver/main/assets/logo.svg&logoColor=white)](https://github.com/seworqs)

