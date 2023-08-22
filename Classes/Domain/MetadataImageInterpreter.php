<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Image;

/**
 * A basic interpreter that uses only the image's metadata for interpretation
 */
#[Flow\Scope('singleton')]
final class MetadataImageInterpreter implements ImageInterpreterInterface
{
    public function interpretImage(Image $image): ImageInterpretation
    {
        return new ImageInterpretation(
            $image->getLabel()
        );
    }
}
