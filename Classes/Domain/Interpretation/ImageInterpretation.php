<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;

#[Flow\Proxy(false)]
final class ImageInterpretation implements \JsonSerializable
{
    public function __construct(
        public readonly ?Locale $locale,
        public readonly ?string $description,
        /** @var array<int,string> */
        public readonly array $labels,
        /** @var array<int,InterpretedObject> */
        public readonly array $objects,
        /** @var array<int,InterpretedText> */
        public readonly array $texts,
        /** @var array<int,InterpretedDominantColor */
        public readonly array $dominantColors,
        /** @var array<int,InterpretedBoundingPolygon> */
        public readonly array $cropHints,
        public readonly ?string $assetId = null
    ) {
    }

    public function getFulltext(): string
    {
        return $this->description
            . ' ' . implode(' ', $this->labels)
            . ' ' . implode(' ', array_map(
                fn (InterpretedText $text): string => $text->text,
                $this->texts
            ));
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'locale' => $this->locale?->getLanguage(),
            'description' => $this->description,
            'labels' => $this->labels,
            'objects' => $this->objects,
            'texts' => $this->texts,
            'dominantColors' => $this->dominantColors,
            'cropHints' => $this->cropHints
        ];
    }
}
