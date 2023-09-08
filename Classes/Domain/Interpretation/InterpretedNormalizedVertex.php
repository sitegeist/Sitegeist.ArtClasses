<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class InterpretedNormalizedVertex implements \JsonSerializable
{
    public function __construct(
        public float $x,
        public float $y
    ) {
        if ($x < 0 || $x > 1) {
            throw new \DomainException('Normalized vertex abscissas must be 0 <= x <= 1, ' . $x . ' given.', 1693843530);
        }
        if ($y < 0 || $y > 1) {
            throw new \DomainException('Normalized vertex ordinates must be 0 <= y <= 1, ' . $y . ' given.', 1693843448);
        }
    }

    /**
     * @param array<string,float> $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array['x'],
            $array['y']
        );
    }

    /**
     * @return array<string,float>
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
