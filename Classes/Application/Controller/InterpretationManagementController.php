<?php

/*
 * This file is part of the Sitegeist.ArtClasses package.
 */

declare(strict_types=1);

namespace Sitegeist\ArtClasses\Application\Controller;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\I18n\Translator;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Fusion\View\FusionView;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Sitegeist\ArtClasses\Domain\Interpretation\ArtClasses;
use Sitegeist\ArtClasses\Domain\Interpretation\ImageInterpretation;

class InterpretationManagementController extends AbstractModuleController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject]
    protected AssetRepository $assetRepository;

    #[Flow\Inject]
    protected ArtClasses $artClasses;

    #[Flow\Inject]
    protected Translator $translator;

    public function indexAction(): void
    {
        $this->view->assignMultiple([
            'interpretations' => array_filter(array_map(
                function (ImageInterpretation $imageInterpretation): ?array {
                    if (!$imageInterpretation->assetId) {
                        return null;
                    }
                    $asset = $this->assetRepository->findByIdentifier($imageInterpretation->assetId);
                    if (!$asset instanceof Asset) {
                        return null;
                    }
                    return [
                        'asset' => $asset,
                        'showUri' => $this->getActionUri('show', ['assetId' => $imageInterpretation->assetId])
                    ];
                },
                $this->artClasses->findAllInterpretations()
            )),
            'labels' => [
                'interpretation' => $this->getLabel('labels.interpretation'),
                'assetLabel' => $this->getLabel('labels.assetLabel'),
                'assetPreview' => $this->getLabel('labels.assetPreview'),
                'actions' => $this->getLabel('labels.actions'),
                'show' => $this->getLabel('labels.show'),
            ]
        ]);
    }

    public function showAction(string $assetId): void
    {
        $this->view->assignMultiple([
            'asset' => $this->assetRepository->findByIdentifier($assetId),
            'interpretation' => $this->artClasses->findInterpretation($assetId),
            'indexUri' => $this->getActionUri('index'),
            'labels' => [
                'interpretation' => $this->getLabel('labels.interpretation'),
                'description' => $this->getLabel('labels.description'),
                'labels' => $this->getLabel('labels.labels'),
                'objects' => $this->getLabel('labels.objects'),
                'texts' => $this->getLabel('labels.texts'),
                'assetPreview' => $this->getLabel('labels.assetPreview'),
                'cropHints' => $this->getLabel('labels.cropHints'),
                'backToIndex' => $this->getLabel('labels.backToIndex'),
            ]
        ]);
    }

    private function getLabel(string $labelId): string
    {
        return $this->translator->translateById(
            $labelId,
            [],
            null,
            null,
            'Modules',
            'Sitegeist.ArtClasses'
        ) ?: $labelId;
    }

    /**
     * @phpstan-param array<mixed,mixed> $parameters
     */
    private function getActionUri(string $actionName, array $parameters = []): Uri
    {
        return new Uri($this->controllerContext
            ->getUriBuilder()
            ->setCreateAbsoluteUri(true)
            ->uriFor(
                $actionName,
                $parameters,
                'InterpretationManagement',
                'Sitegeist.ArtClasses',
                'Application'
            ));
    }

    protected function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);
        /** @var FusionView $view */
        $view->setFusionPathPattern(
            'resource://Sitegeist.ArtClasses/Private/Fusion/Integration/InterpretationManagement'
        );
        $view->assignMultiple([
            'flashMessages' => $this->controllerContext->getFlashMessageContainer()->getMessagesAndFlush()
        ]);
    }
}
