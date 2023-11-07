<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Tagging;

interface ObjectTaggerInterface
{
    /**
     * Tags an object by mapping the interpreted to the available tags and returns the matches
     *
     * @param array<string> $interpretedTags
     * @param array<string> $availableTags
     * @return array<string>
     */
    public function tagObject(array $interpretedTags, array $availableTags): array;
}
