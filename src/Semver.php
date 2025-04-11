<?php

namespace Seworqs\Semver;

use Seworqs\Semver\Enum\EnumBumpPreReleaseType;
use Seworqs\Semver\Enum\EnumBumpReleaseType;

class Semver {

    private string $currentVersion;

    public function __construct(
        private readonly int $major,
        private readonly int $minor,
        private readonly int $patch,
        private readonly ?EnumBumpPreReleaseType $preReleaseType = null,
        private readonly int $preReleaseNumber = 0,
        private readonly string $preReleaseDelimiter = '.',
    ) {

    }

    public static function fromString(string $currentVersion): Semver {

        $preReleaseType = null;
        $preReleaseNumber = 0;
        $preReleaseDelimiter = '';

        // Get release and pre release.
        [$core, $pre] = array_pad(explode('-', $currentVersion, 2), 2, null);

        // Is it a pre release?
        if ($pre) {

            // Get pre release info.
            preg_match('/^(?<type>[a-zA-Z]+)(?<delim>\.)?(?<number>\d+)$/', $pre, $matches);

            $preReleaseType = EnumBumpPreReleaseType::tryFrom($matches['type']);
            $preReleaseDelimiter = $matches['delim'] ?? '';
            $preReleaseNumber = (int) $matches['number'];
        }

        [$major, $minor, $patch] = explode('.', $core);

        return new self(
            $major,
            $minor,
            $patch,
            $preReleaseType,
            $preReleaseNumber,
            $preReleaseDelimiter
        );
    }

    public function __toString(): string {
        $parts = [$this->major, $this->minor,$this->patch];
        $currentVersion = implode('.', $parts);

        if ($this->preReleaseType) {
            $preParts = [$this->preReleaseType->value, $this->preReleaseNumber];
            $currentVersion .= '-' . implode($this->preReleaseDelimiter, $preParts);
        }
        return  $currentVersion;
    }

    public function toString(): string {
        return (string) $this;
    }

    public function isPreRelease(): bool {
        return $this->preReleaseType !== null;
    }

    public function compareVersionTo(Semver $other): int
    {
        foreach (['major', 'minor', 'patch'] as $part) {
            if ($this->$part > $other->$part) {
                return 1;
            }
            if ($this->$part < $other->$part) {
                return -1;
            }
        }

        // Same type, check pre/release.
        if ($this->preReleaseType === null && $other->preReleaseType !== null) {
            return 1; // stable > pre-release
        }

        if ($this->preReleaseType !== null && $other->preReleaseType === null) {
            return -1; // pre-release < stable
        }

        if ($this->preReleaseType && $other->preReleaseType) {
            $rankA = $this->preReleaseType->rank();
            $rankB = $other->preReleaseType->rank();

            if ($rankA > $rankB) return 1;
            if ($rankA < $rankB) return -1;

            // Same type, check pre/release.
            return $this->preReleaseNumber <=> $other->preReleaseNumber;
        }

        // Equal
        return 0;
    }

    public function isVersionNewerThan(Semver $other): bool
    {
        return $this->compareVersionTo($other) > 0;
    }

    public function isSameVersionAs(Semver $other): bool {

//        return $this->major === $other->major
//            && $this->minor === $other->minor
//            && $this->patch === $other->patch
//            && $this->preReleaseType === $other->preReleaseType
//            && $this->preReleaseNumber === $other->preReleaseNumber;
        return $this->compareVersionTo($other) === 0;
    }

    public function isVersionIdenticalTo(Semver $other):bool {
        return $this->isSameVersionAs($other)
            && $this->preReleaseDelimiter === $other->preReleaseDelimiter;
    }

    public function isVersionOlderThan(Semver $other):bool {
        return $this->compareVersionTo($other) > 0;
    }

    public function withPreReleaseDelimiter(string $preReleaseDelimiter = '.'): self {
        if ($preReleaseDelimiter === $this->preReleaseDelimiter) {
            return $this;
        }

        return new self(
            $this->major,
            $this->minor,
            $this->patch,
            $this->preReleaseType,
            $this->preReleaseNumber,
            $preReleaseDelimiter
        );
    }


    public function removePreReleaseDelimiter(): self {
        return $this->withPreReleaseDelimiter('');
    }

    public function bumpVersion(EnumBumpPreReleaseType|EnumBumpReleaseType $releaseType, ?EnumBumpPreReleaseType $preReleaseType = null) {

        $next = null;

        // Only a pre-release bump.
        if ($releaseType instanceof EnumBumpPreReleaseType && $preReleaseType === null) {
            $preType = $releaseType;
            $preNum = 1;

            if ($this->preReleaseType === $preType) {
                $preNum = $this->preReleaseNumber + 1;
            }

            $next = new self(
                $this->major,
                $this->minor,
                $this->patch,
                $preType,
                $preNum,
                $this->preReleaseDelimiter
            );
        }

        // Complete bump.
        if ($releaseType instanceof EnumBumpReleaseType) {
            $major = $this->major;
            $minor = $this->minor;
            $patch = $this->patch;

            // Bump
            switch ($releaseType) {
                case EnumBumpReleaseType::MAJOR:
                    $major++;
                    $minor = 0;
                    $patch = 0;
                    break;
                case EnumBumpReleaseType::MINOR:
                    $minor++;
                    $patch = 0;
                    break;
                case EnumBumpReleaseType::PATCH:
                    $patch++;
                    break;
                case EnumBumpReleaseType::STABLE:
                    // Remove pre-release, return directly.
                    return new self($major, $minor, $patch,);
            }

            // Handle pre-release (if any).
            $preType = $preReleaseType;
            $preNum = $preType ? 1 : 0;

            $next = new self(
                $major,
                $minor,
                $patch,
                $preType,
                $preNum,
                $this->preReleaseDelimiter
            );
        }

        // Do we have a new version?
        if (!$next) {
            throw new \InvalidArgumentException('Invalid bump parameters.');
        }

        // Check if we are on track.
        if ($next->compareVersionTo($this) <= 0) {
            throw new \LogicException("We can not bump backwards. ({$this} → {$next})");
        }

        return $next;
    }

    //////////
    /// DEPRECATED FUNCTIONS
    //////////

    /**
     * @deprecated since 1.0.2 Please use withPreReleaseDelimiter(). Will be removed in next major release.
     *
     * @param string $preReleaseDelimiter
     * @return $this|self
     */
    public function withDelimiter(string $preReleaseDelimiter = '.'): self {

        trigger_error("The use of withDelimiter() is deprecated. Please use with withPreReleaseDelimiter().");

        if ($preReleaseDelimiter === $this->preReleaseDelimiter) {
            return $this;
        }

        return new self(
            $this->major,
            $this->minor,
            $this->patch,
            $this->preReleaseType,
            $this->preReleaseNumber,
            $preReleaseDelimiter
        );
    }

    /**
     * @deprecated since 1.0.2 Please use removePreReleaseDelimiter(). Will be removed in next major release.
     *
     * @return $this
     */
    public function removeDelimiter(): self {
        trigger_error("The use of removeDelimiter() is deprecated. Please use with removePreReleaseDelimiter().");

        return $this->withDelimiter('');
    }

}