## Usage examples

> When you use `$semver->bump(...)` it will create a new instance and leave the current instance unchanged!

## Basic 
```php
use Seworqs\Semver;
use Seworqs\Semver\Enum\EnumBumpReleaseType;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;

// Create a semver from a string.
$semver = Semver::fromString('2.5.3-beta.1');

// Bump BETA (2.5.3-beta.1 => 2.5.3-beta.2)
$semverBeta = $semver->bump(EnumBumpPreReleaseType::BETA);

// Bump RC (2.5.3-beta.1 => 2.5.3-rc.1)
$semverRC = $semver->bump(EnumBumpPreReleaseType::BETA);

// Bump PATCH (2.5.3-beta.1 => 2.5.4)
$semverPatched = $semver->bump(EnumBumpReleaseType::PATCH);

// Bump MINOR (2.5.3-beta.1 => 2.6.0)
$semverMinor = $semver->bump(EnumBumpReleaseType::MINOR);

// Bump MAJOR (2.5.3-beta.1 => 3.0.0)
$semverMajor = $semver->bump(EnumBumpReleaseType::MAJOR);

// Get the version of a semver instance.
$versionA = $semver->toString(); // '2.5.3-beta.1'
$versionB = (string) $semver; // '2.5.3-beta.1'
```

## Advanced
```php
use Seworqs\Semver;
use Seworqs\Semver\Enum\EnumBumpReleaseType;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;

// Create a semver from a string.
$semver = Semver::fromString('2.5.3-beta.1');

/*
 * We can also bump a release (PATCH, MINOR, MAJOR) and start a pre-release (DEV, ALPHA, BETA, RC)
 */

// Bump MAJOR (2.5.3-beta.1 => 3.0.0-alpha.1)
$semverMajorAlpha = $semver->bump(EnumBumpReleaseType::MAJOR, EnumBumpPreReleaseType::ALPHA);

/*
 * Composer uses a little different notation for pre-releases. It ommits the dot for the pre-release sequence number, like: 2.5.3-beta.1 => 2.5.3-beta1.
 * We can handle that format also.
 */

// Use Composer like versioning.
$semverComposer = Semver::fromString('2.5.3-beta1');

/*
 * Even if you have the version with a dot, you can remove it. The instance itself will not change though!
 */

// 2.5.3-beta.1 => 2.5.3-beta1
$semver->removeDelimiter()->toString();

/*
 * do you want to convert it to a Composer like version?
 */

// 2.5.3-beta.1 => 2.5.3-beta1
$semverRemovedDot = $semver->bump(EnumBumpPreReleaseType::BETA);

/*
 * You can also use the `removeDelimiter` when bumping. The new instance will not have the delimiter.
 */
 
// 2.5.3-beta.1 => 2.5.3-beta2
$semverRemovedDotBumped = $semver->bump(EnumBumpPreReleaseType::BETA);


/*
 * Comparing two instances. Using instances we created earlier.
 * 
 * $semver => 2.5.3.alpha.1
 * $semverBeta => 2.5.3-beta.2
 * $semverRC => 2.5.3-rc.1
 * $semverPatched => 2.5.4
 * $semverMinor => 2.6.0
 * $semverMajor => 3.0.0
 * $semverComposer => 2.5.3-alpha1
 */

// Returns -1, because the version is older than the beta version.
$semver->compareVersionTo($semverBeta);

// Returns 0, because the version is the same.
$semver->compareVersionTo($semverComposer);

// Returns 1, because the version is newer than the beta version.
$semver->compareVersionTo($semverBeta);

/* 
 * To make live a bit easier, we have some wrappers around the `compareVersionTo`.
 */

// This is true.
$semver->isVersionOlderThan($semverBeta);

// This is true. The (missing) dot is only cosmetic!
$semver->isSameVersionAs($semverComposer); 

// This is false.
$semver->isVersionNewerThan($semverBeta);

/*
 * While it can be the same version, the version don't have to be identical. Think about the Composer versioning!
 * 2.5.3-alpha.1 is the same version as 2.5.3-alpha1, but they are NOT identical!
 */

// This is false! Due to the missing dot they are not identical!
$semver->isIdentical($semverCOmposer); 
```
