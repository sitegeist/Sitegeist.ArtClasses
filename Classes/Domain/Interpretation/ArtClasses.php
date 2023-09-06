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
                \json_decode($dataset['interpretation'], true, 512, JSON_THROW_ON_ERROR)
            )
            : null;
    }

    /**
     * @return array<string>
     */
    public function findAssetIds(string $searchTerm): array
    {
        return [];
    }

    /**
     * @param array<string,mixed> $dataset
     */
    private function mapDatasetToImageInterpretation(array $dataset): ImageInterpretation
    {
        return new ImageInterpretation(
            $dataset['locale'] ? new Locale($dataset['locale']) : null,
            $dataset['description'],
            [],
            [],
            [],
            [],
            [],
        );
    }
}
