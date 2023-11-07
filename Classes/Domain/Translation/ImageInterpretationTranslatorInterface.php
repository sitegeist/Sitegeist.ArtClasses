<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Translation;

use Neos\Flow\I18n\Locale;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpretation;

interface ImageInterpretationTranslatorInterface
{
    /**
     * If no source locale is given, the translator will try to automatically detect it.
     * Might not be supported by all adapters which then should throw the corresponding exception.
     *
     * @throws SourceLocaleCouldNotBeAutoDetected
     */
    public function translateImageInterpretation(
        ImageInterpretation $imageInterpretation,
        ?Locale $sourceLocale,
        Locale $targetLocale
    ): ImageInterpretation;

    /**
     * Translates a given set of tags to the target locale.
     * If no source locale is given, the translator will try to automatically detect it.
     *
     * The returned array must be indexed with the same tag IDs that are given in the input array.
     *
     * @param array<string,string> $tags
     * @return array<string,string>
     */
    public function translateTags(
        array $tags,
        ?Locale $sourceLocale,
        Locale $targetLocale
    ): array;
}
