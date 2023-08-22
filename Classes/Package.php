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

class Package extends BasePackage
{
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

        $signalSlotDispatcher->connect(
            AssetService::class,
            'assetCreated',
            AssetHandler::class,
            'whenAssetWasCreated'
        );
    }
}
