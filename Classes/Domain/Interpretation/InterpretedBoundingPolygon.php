<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class InterpretedBoundingPolygon implements \JsonSerializable
{
    public function __construct(
        /** @var array<int,InterpretedVertex> */
        public array $vertices,
        /** @var array<int,InterpretedNormalizedVertex> */
        public array $normalizedVertices
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
