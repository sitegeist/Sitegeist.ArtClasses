<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Domain\Interpretation;

use Doctrine\DBAL\Connection;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;
use Neos\Flow\Persistence\Doctrine\ConnectionFactory;

#[Flow\Scope('singleton')]
final class ArtClasses
{
    private const TABLE_NAME = 'sitegeist_artclasses_imageinterpretation';

    private readonly Connection $databaseConnection;

    public function __construct(
        ConnectionFactory $connectionFactory
    ) {
        $this->databaseConnection = $connectionFactory->create();
    }

    public function addInterpretation(string $assetId, ImageInterpretation $imageInterpretation): void
    {
        $this->databaseConnection->insert(
            self::TABLE_NAME,
            [
                'asset_id' => $assetId,
                'interpretation' => \json_encode($imageInterpretation, JSON_THROW_ON_ERROR),
                'full_text' => $imageInterpretation->getFulltext()
            ]
        );
    }

    public function replaceInterpretation(string $assetId, ImageInterpretation $imageInterpretation): void
    {
        $this->databaseConnection->update(
            self::TABLE_NAME,
            [
                'interpretation' => \json_encode($imageInterpretation, JSON_THROW_ON_ERROR),
                'full_text' => $imageInterpretation->getFulltext()
            ],
            [
                'asset_id' => $assetId
            ]
        );
    }

    public function removeInterpretation(string $assetId): void
    {
        $this->databaseConnection->delete(
            self::TABLE_NAME,
            [
                'asset_id' => $assetId
            ]
        );
    }

    public function hasInterpretation(string $assetId): bool
    {
        $dataset = $this->databaseConnection->executeQuery(
            'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE asset_id = :assetId',
            [
                'assetId' => $assetId
            ]
        )->fetchAssociative();

        return (int)$dataset['COUNT(*)'] > 0;
    }

    /**
     * @return array<int,ImageInterpretation>
     */
    public function findAllInterpretations(): array
    {
        $interpretations = [];
        foreach (
            $this->databaseConnection->executeQuery(
                'SELECT asset_id, interpretation FROM ' . self::TABLE_NAME
            )->fetchAllAssociative() as $dataset
        ) {
            $interpretations[] = $this->mapDatasetToImageInterpretation(
                $dataset['asset_id'],
                \json_decode($dataset['interpretation'], true, 512, JSON_THROW_ON_ERROR)
            );
        }

        return $interpretations;
    }

    public function findInterpretation(string $assetId): ?ImageInterpretation
    {
        $dataset = $this->databaseConnection->executeQuery(
            'SELECT interpretation FROM ' . self::TABLE_NAME . ' WHERE asset_id = :assetId',
            [
                'assetId' => $assetId
            ]
        )->fetchAssociative();

        return $dataset
            ? $this->mapDatasetToImageInterpretation(
                $assetId,
                \json_decode($dataset['interpretation'], true, 512, JSON_THROW_ON_ERROR)
            )
            : null;
    }

    /**
     * @return array<string>
     */
    public function findAssetIds(string $searchTerm): array
    {
        return array_map(
            fn (array $record): string => $record['asset_id'],
            $this->databaseConnection->executeQuery(
                'SELECT asset_id FROM ' . self::TABLE_NAME . ' WHERE interpretation LIKE :searchTerm',
                [
                    'searchTerm' => '%' . $searchTerm . '%'
                ]
            )->fetchAllAssociative()
        );
    }

    /**
     * @param array<string,mixed> $dataset
     */
    private function mapDatasetToImageInterpretation(string $assetId, array $dataset): ImageInterpretation
    {
        return new ImageInterpretation(
            $dataset['locale'] ? new Locale($dataset['locale']) : null,
            $dataset['description'],
            $dataset['labels'],
            array_map(
                fn (array $objectData): InterpretedObject => new InterpretedObject(
                    $objectData['name'],
                    InterpretedBoundingPolygon::fromArray($objectData['boundingPolygon'])
                ),
                $dataset['objects']
            ),
            array_map(
                fn (array $textData): InterpretedText => new InterpretedText(
                    $textData['text'],
                    $textData['locale'] ? new Locale($textData['locale']) : null,
                    InterpretedBoundingPolygon::fromArray($textData['boundingPolygon'])
                ),
                $dataset['texts']
            ),
            array_map(
                fn (array $dominantColorData): InterpretedDominantColor
                    => InterpretedDominantColor::fromArray($dominantColorData),
                $dataset['dominantColors']
            ),
            array_map(
                fn (array $cropHintData): InterpretedBoundingPolygon
                    => InterpretedBoundingPolygon::fromArray($cropHintData),
                $dataset['cropHints']
            ),
            $assetId
        );
    }
}
