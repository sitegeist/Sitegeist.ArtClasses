<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses;

use Neos\Flow\Core\Booting\Sequence;
use Neos\Flow\Core\Booting\Step;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Media\Domain\Service\AssetService;
use Sitegeist\ArtClasses\Domain\AssetHandler;
use Neos\Flow\Annotations as Flow;

class Package extends BasePackage
{
    /**
     * @Flow\InjectConfiguration(path="enableImageTagging")
     * @var string
     */
    protected $enableImageTagging;

    public function boot(Bootstrap $bootstrap): void
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $package = $this;
        $dispatcher->connect(Sequence::class, 'afterInvokeStep', function (Step $step) use ($package, $bootstrap) {
            if ($step->getIdentifier() === 'neos.flow:objectmanagement:runtime') {
                $package->registerAssetHandlingSlots($bootstrap);
            }
        });
    }

    public function registerAssetHandlingSlots(Bootstrap $bootstrap): void
    {
        $signalSlotDispatcher = $bootstrap->getSignalSlotDispatcher();

        if ($this->enableImageTagging) {
            $signalSlotDispatcher->connect(
                AssetService::class,
                'assetCreated',
                AssetHandler::class,
                'whenAssetWasCreatedOrAssetResourceWasReplaced'
            );
            $signalSlotDispatcher->connect(
                AssetService::class,
                'assetRemoved',
                AssetHandler::class,
                'whenAssetWasRemoved'
            );
            $signalSlotDispatcher->connect(
                AssetService::class,
                'assetResourceReplaced',
                AssetHandler::class,
                'whenAssetWasCreatedOrAssetResourceWasReplaced'
            );
        }
    }
}
