<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\ImageRepository;
use Neos\Media\Domain\Repository\TagRepository;
use Sitegeist\ArtClasses\Domain\Interpretation\ArtClasses;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpretation;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpreterInterface;
use Sitegeist\ArtClasses\Domain\Tagging\ObjectTaggerInterface;
use Sitegeist\ArtClasses\Domain\Translation\ImageInterpretationTranslatorInterface;

#[Flow\Scope('singleton')]
class AssetHandler
{
    public function __construct(
        private readonly ImageInterpreterInterface $imageInterpreter,
        private readonly ImageInterpretationTranslatorInterface $imageInterpretationTranslator,
        private readonly ImageRepository $imageRepository,
        private readonly ArtClasses $artClasses,
        private readonly ObjectTaggerInterface $tagger,
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function whenAssetWasCreatedOrAssetResourceWasReplaced(Asset $asset): void
    {
        if ($asset instanceof Image) {
            $targetLocale = new Locale('de');
            $imageInterpretation = $this->imageInterpreter->interpretImage($asset, $targetLocale);
            if ((string)$imageInterpretation->locale !== (string)$targetLocale) {
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

            if (!empty($imageInterpretation->labels)) {
                $tags = $this->resolveTags($imageInterpretation, $asset);
                if ($tags->count() > 0) {
                    $asset->setTags($tags);
                }
            }
            if ($imageInterpretation->description) {
                $asset->setCaption($imageInterpretation->description);
                $this->imageRepository->update($asset);
            }
        }
    }

    /**
     * @return ArrayCollection<int,Tag>
     */
    private function resolveTags(ImageInterpretation $imageInterpretation, Asset $asset): ArrayCollection
    {
        $tagsToUse = [];
        $availableTags = [];
        $availableTagNames = [];
        foreach (
            $this->tagRepository->findByAssetCollections(
                $asset->getAssetCollections()->toArray()
            )->toArray() as $availableTag
        ) {
            /** @var Tag $availableTag */
            $availableTags[$availableTag->getLabel()] = $availableTag;
            $availableTagNames[$availableTag->getLabel()] = $availableTag->getLabel();
        }
        if (!empty($availableTags)) {
            $englishTags = $this->imageInterpretationTranslator->translateTags($availableTagNames, null, new Locale('en'));
            $interpretedTags = \str_starts_with((string)$imageInterpretation->locale, 'en')
                ? $imageInterpretation->labels
                : $this->imageInterpretationTranslator->translateTags($imageInterpretation->labels, null, new Locale('en'));

            $usableEnglishTagNames = $this->tagger->tagObject($interpretedTags, $englishTags);

            foreach ($usableEnglishTagNames as $englishTagName) {
                $tagName = array_search($englishTagName, $englishTags);
                if ($tagName && array_key_exists($tagName, $availableTags)) {
                    $tagsToUse[] = $availableTags[$tagName];
                }
            }
        }

        return new ArrayCollection($tagsToUse);
    }

    public function whenAssetWasRemoved(Asset $asset): void
    {
        $this->artClasses->removeInterpretation($asset->getIdentifier());
    }
}
