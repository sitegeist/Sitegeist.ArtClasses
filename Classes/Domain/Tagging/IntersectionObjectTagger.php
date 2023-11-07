<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Tagging;

use Neos\Flow\Annotations as Flow;

/**
 * A basic object tagger that only allows exact matches
 */
#[Flow\Scope('singleton')]
final class IntersectionObjectTagger implements ObjectTaggerInterface
{
    /**
     * @param array<string> $interpretedTags
     * @param array<string> $availableTags
     * @return array<string>
     */
    public function tagObject(array $interpretedTags, array $availableTags): array
    {
        return array_intersect($interpretedTags, $availableTags);
    }
}
