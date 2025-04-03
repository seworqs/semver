<?php

namespace Seworqs\Semver\Enum;

enum EnumBumpReleaseType: string {
    case MAJOR  = 'major';
    case MINOR  = 'minor';
    case PATCH  = 'patch';
    case STABLE = 'stable';

}