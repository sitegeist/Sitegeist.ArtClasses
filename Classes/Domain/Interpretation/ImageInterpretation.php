<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;

#[Flow\Proxy(false)]
final class ImageInterpretation
{
    public function __construct(
        public readonly ?Locale $locale,
        public readonly ?string $description
    ) {
    }
}
