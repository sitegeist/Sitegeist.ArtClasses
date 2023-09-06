<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class InterpretedDominantColor implements \JsonSerializable
{
    public function __construct(
        public float $red,
        public float $green,
        public float $blue,
        public float $alpha
    ) {
    }

    /**
     * @return array<string,float>
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
