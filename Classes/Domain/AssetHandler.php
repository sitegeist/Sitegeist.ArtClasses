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
use Sitegeist\ArtClasses\Domain\Interpretation\ArtClasses;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpreterInterface;
use Sitegeist\ArtClasses\Domain\Translation\ImageInterpretationTranslatorInterface;

#[Flow\Scope('singleton')]
class AssetHandler
{
    public function __construct(
        private readonly ImageInterpreterInterface $imageInterpreter,
        private readonly ImageInterpretationTranslatorInterface $imageInterpretationTranslator,
        private readonly ImageRepository $imageRepository,
        private readonly ArtClasses $artClasses
    ) {
    }

    public function whenAssetWasCreated(Asset $asset): void
    {
        if ($asset instanceof Image) {
            $targetLocale = new Locale('de');
            $imageInterpretation = $this->imageInterpreter->interpretImage($asset, $targetLocale);
            if ($imageInterpretation->locale !== $targetLocale) {
                $imageInterpretation = $this->imageInterpretationTranslator->translateImageInterpretation(
                    $imageInterpretation,
                    $imageInterpretation->locale,
                    $targetLocale
                );
            }
            if ($this->artClasses->hasInterpretation($asset->getIdentifier())) {
                $this->artClasses->replaceInterpretation($asset->getIdentifier(), $imageInterpretation);
            } else {
                $this->artClasses->addInterpretation($asset->getIdentifier(), $imageInterpretation);
            }
            if ($imageInterpretation->description) {
                $asset->setCaption($imageInterpretation->description);
                $this->imageRepository->update($asset);
            }
        }
    }
}
