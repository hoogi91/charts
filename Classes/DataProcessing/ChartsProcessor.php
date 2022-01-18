<?php

namespace Hoogi91\Charts\DataProcessing;

use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs as ChartJsLibrary;
use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class ChartsProcessor implements DataProcessorInterface
{

    private PageRenderer $pageRenderer;

    private ChartDataRepository $chartDataRepository;

    private LibraryInterface $chartLibrary;

    public function __construct(
        PageRenderer $pageRenderer,
        ChartDataRepository $repository,
        ExtensionConfiguration $extConf,
        LibraryRegistry $registry
    ) {
        $this->pageRenderer = $pageRenderer;
        $this->chartDataRepository = $repository;
        $this->chartLibrary = $registry->getLibrary(
            (string)($extConf->get('charts', 'library') ?: ChartJsLibrary::getServiceIndex())
        );
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // evaluate options from configuration
        $chartIdentifier = uniqid('chart-', true);
        $chartType = $processedData['data']['CType'] ?? '';
        $includeAssets = (bool)$cObj->stdWrapValue('assets', $processorConfiguration, '1');
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'chart');

        // the chart data uid to load
        $dataUid = (int)$cObj->stdWrapValue('data', $processorConfiguration, '0');
        if (empty($dataUid)) {
            $processedData[$targetVariableName] = [
                'identifier' => $chartIdentifier,
                'type' => $chartType,
                'library' => $this->chartLibrary->getName(),
                'entity' => null,
            ];
            return $processedData;
        }

        /** @var ChartData $chartEntity */
        $chartEntity = $this->chartDataRepository->findByUid($dataUid);
        $chartData = [
            'identifier' => $chartIdentifier,
            'type' => $chartType,
            'library' => $this->chartLibrary->getName(),
            'entity' => $chartEntity,
        ];

        // render assets of this
        if ($includeAssets === true) {
            $this->chartLibrary->getStylesheetAssets($chartType, $this->pageRenderer);
            $this->chartLibrary->getJavascriptAssets($chartType, $this->pageRenderer);
            $this->chartLibrary->getEntityStylesheet($chartIdentifier, $chartType, $chartEntity, $this->pageRenderer);
            $this->chartLibrary->getEntityJavascript($chartIdentifier, $chartType, $chartEntity, $this->pageRenderer);
        } else {
            $chartData['assets']['css']['libs'] = $this->chartLibrary->getStylesheetAssets($chartType);
            $chartData['assets']['css']['entity'][] = $this->chartLibrary->getEntityStylesheet(
                $chartIdentifier,
                $chartType,
                $chartEntity
            );
            $chartData['assets']['js']['libs'] = $this->chartLibrary->getJavascriptAssets($chartType);
            $chartData['assets']['js']['entity'][] = $this->chartLibrary->getEntityJavascript(
                $chartIdentifier,
                $chartType,
                $chartEntity
            );
        }

        // assign to current processed data and return
        $processedData[$targetVariableName] = $chartData;
        return $processedData;
    }
}
