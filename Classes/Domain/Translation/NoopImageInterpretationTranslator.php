<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Translation;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpretation;

/**
 * A basic image interpretation translator that simply does nothing
 */
#[Flow\Scope('singleton')]
final class NoopImageInterpretationTranslator implements ImageInterpretationTranslatorInterface
{
    public function translateImageInterpretation(
        ImageInterpretation $imageInterpretation,
        ?Locale $sourceLocale,
        Locale $targetLocale
    ): ImageInterpretation
    {
        return $imageInterpretation;
    }

    /**
     * @param array<string,string> $tags
     * @return array<string,string>
     */
    public function translateTags(array $tags, ?Locale $sourceLocale, Locale $targetLocale): array
    {
        return $tags;
    }
}
