<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class InterpretedVertex implements \JsonSerializable
{
    public function __construct(
        public int $x,
        public int $y
    ) {
    }

    /**
     * @param array<string,int> $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array['x'],
            $array['y']
        );
    }

    /**
     * @return array<string,int>
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
