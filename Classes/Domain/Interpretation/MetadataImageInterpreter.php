<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;
use Neos\Media\Domain\Model\Image;

/**
 * A basic interpreter that uses only the image's metadata for interpretation
 */
#[Flow\Scope('singleton')]
final class MetadataImageInterpreter implements ImageInterpreterInterface
{
    public function interpretImage(Image $image, ?Locale $targetLocale): ImageInterpretation
    {
        return new ImageInterpretation(
            null,
            $image->getLabel(),
            [],
            [],
            [],
            [],
            []
        );
    }
}
