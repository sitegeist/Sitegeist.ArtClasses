<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Aspect;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\AssetRepository;
use Sitegeist\ArtClasses\Domain\Interpretation\ArtClasses;

#[Flow\Aspect]
#[Flow\Scope('singleton')]
class AssetRepositoryAspect extends AssetRepository
{
    const ENTITY_CLASSNAME = Asset::class;

    #[Flow\Inject]
    protected ArtClasses $artClasses;

    #[Flow\Around("method(Neos\Media\Domain\Repository\AssetRepository->findBySearchTermOrTags())")]
    public function adjustFindBySearchTermOrTags(JoinPointInterface $joinPoint): QueryResultInterface
    {
        /** @var string $searchTerm */
        $searchTerm = $joinPoint->getMethodArgument('searchTerm');
        /** @var array<Tag> $tags */
        $tags = $joinPoint->getMethodArgument('tags');
        /** @var ?AssetCollection $assetCollection */
        $assetCollection = $joinPoint->getMethodArgument('assetCollection');
        $query = $this->createQuery();

        $matchingAssetIds = $this->artClasses->findAssetIds($searchTerm);

        $constraints = [
            $query->like('title', '%' . $searchTerm . '%'),
            $query->like('resource.filename', '%' . $searchTerm . '%'),
            $query->like('caption', '%' . $searchTerm . '%'),
            $query->in('Persistence_Object_Identifier', $matchingAssetIds)
        ];
        foreach ($tags as $tag) {
            $constraints[] = $query->contains('tags', $tag);
        }
        $query->matching($query->logicalOr($constraints));
        $this->addAssetVariantFilterClause($query);
        $this->addAssetCollectionToQueryConstraints($query, $assetCollection);
        return $query->execute();
    }
}
