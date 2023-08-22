<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Translation;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class SourceLocaleCouldNotBeAutoDetected extends \RuntimeException
{
    public static function butWasSupposedTo(): self
    {
        return new self(
            'No source locale was given and it could not be auto-detected',
            1692716148
        );
    }
}
