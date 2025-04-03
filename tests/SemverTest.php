<?php

Namespace Seworqs\Semver\Test;

use PHPUnit\Framework\TestCase;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;
use Seworqs\Semver\Semver;

class SemverTest extends TestCase {

    public function testVersioning() {

        $semver = Semver::fromString('0.0.1');
        $this->assertEquals('0.0.1', $semver->toString());
        $this->assertFalse($semver->isPreRelease());

        $semver2 = Semver::fromString('0.0.1');
        $this->assertTrue($semver->isSameVersionAs($semver2));
        $this->assertTrue($semver->isVersionIdenticalTo($semver2));
        $this->assertFalse($semver->isVersionOlderThan($semver2));
        $this->assertFalse($semver->isVersionNewerThan($semver2));

        $semver = Semver::fromString('0.0.1-beta.2');
        $semver2 = Semver::fromString('0.0.1-beta2');
        $this->assertTrue($semver->isSameVersionAs($semver2)); // Same version
        $this->assertFalse($semver->isVersionIdenticalTo($semver2)); // But not identical!
        $this->assertFalse($semver->isVersionOlderThan($semver2));
        $this->assertFalse($semver->isVersionNewerThan($semver2));

        $semver = Semver::fromString('1.0.5-beta.2');
        $this->assertEquals('1.0.5-beta.2', $semver->toString());

        $semver = Semver::fromString('1.0.5-beta6');
        $this->assertEquals('1.0.5-beta6', $semver->toString());

        $semver = Semver::fromString('1.0.6');
        $this->assertEquals('1.0.6', $semver->toString());
    }

    public function testBackwards() {

        $this->expectException(\LogicException::class);

        $semver = Semver::fromString('1.0.0');
        $newVersion = $semver->removeDelimiter()->bumpVersion(EnumBumpPreReleaseType::ALPHA);
    }

    public function testPreRelease() {

        $semver = Semver::fromString('1.2.3');
        $newVersion = $semver->withDelimiter('.')->bumpVersion(\Seworqs\Semver\Enum\EnumBumpReleaseType::MINOR, EnumBumpPreReleaseType::ALPHA);

        $this->assertEquals($newVersion->toString(), '1.3.0-alpha.1');
    }
}