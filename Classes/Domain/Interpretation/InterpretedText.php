<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;

#[Flow\Proxy(false)]
final class InterpretedText implements \JsonSerializable
{
    public function __construct(
        public string $text,
        public ?Locale $locale,
        public InterpretedBoundingPolygon $boundingPolygon
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'locale' => $this->locale->getLanguage(),
            'boundingPolygon' => $this->boundingPolygon
        ];
    }
}
