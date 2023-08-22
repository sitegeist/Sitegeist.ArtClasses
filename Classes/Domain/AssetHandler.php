<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\ImageRepository;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpreterInterface;
use Sitegeist\ArtClasses\Domain\Translation\ImageInterpretationTranslatorInterface;

#[Flow\Scope('singleton')]
class AssetHandler
{
    public function __construct(
        private readonly ImageInterpreterInterface $imageInterpreter,
        private readonly ImageInterpretationTranslatorInterface $imageInterpretationTranslator,
        private readonly ImageRepository $imageRepository
    ) {
    }

    public function whenAssetWasCreated(Asset $asset): void
    {
        if ($asset instanceof Image) {
            $imageInterpretation = $this->imageInterpretationTranslator->translateImageInterpretation(
                $this->imageInterpreter->interpretImage($asset),
                null,
                new Locale('de')
            );
            if ($imageInterpretation->description) {
                $asset->setCaption($imageInterpretation->description);
                $this->imageRepository->update($asset);
            }
        }
    }
}
