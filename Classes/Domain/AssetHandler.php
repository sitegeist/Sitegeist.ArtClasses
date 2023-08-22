<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\ImageRepository;

#[Flow\Scope('singleton')]
class AssetHandler
{
    public function __construct(
        private readonly ImageInterpreterInterface $imageInterpreter,
        private readonly ImageRepository $imageRepository
    ) {
    }

    public function whenAssetWasCreated(Asset $asset): void
    {
        if ($asset instanceof Image) {
            $imageInterpretation = $this->imageInterpreter->interpretImage($asset);
            if ($imageInterpretation->description) {
                $asset->setCaption($imageInterpretation->description);
                $this->imageRepository->update($asset);
            }
        }
    }
}
