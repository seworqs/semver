<?php

namespace Seworqs\Semver\Enum;

enum EnumBumpPreReleaseType: string {
    case DEV = 'dev';
    case ALPHA = 'alpha';
    case BETA  = 'beta';
    case RC    = 'rc';

    public function rank(): int{

        return match($this) {
            self::DEV => 0,
            self::ALPHA => 1,
            self::BETA => 2,
            self::RC => 3,
        };
    }


}