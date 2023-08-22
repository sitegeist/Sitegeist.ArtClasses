<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Media\Domain\Model\Image;

interface ImageInterpreterInterface
{
    public function interpretImage(Image $image): ImageInterpretation;
}
