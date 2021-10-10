<?php

namespace Hoogi91\Charts\DataProcessing;

use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs as ChartJsLibrary;
use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use Hoogi91\Charts\RegisterChartLibraryException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Class ChartsProcessor
 * @package Hoogi91\Charts\DataProcessing
 */
class ChartsProcessor implements DataProcessorInterface
{

    /**
     * @var PageRenderer
     */
    private $pageRenderer;

    /**
     * @var ChartDataRepository
     */
    private $chartDataRepository;

    /**
     * @var LibraryInterface
     */
    private $chartLibrary;

    /**
     * ChartsProcessor constructor.
     * @throws RegisterChartLibraryException
     */
    public function __construct(PageRenderer $pageRenderer, ChartDataRepository $repository, LibraryRegistry $registry)
    {
        $this->pageRenderer = $pageRenderer;
        $this->chartDataRepository = $repository;

        $this->chartLibrary = $registry->getLibrary($this->getChartLibrary());
        if (!$this->chartLibrary instanceof LibraryInterface) {
            throw new RegisterChartLibraryException(
                sprintf(
                    'Evaluated Chart Library "%s" doesn\'t exist or doesn\'t implement "%s"',
                    $this->getChartLibrary(),
                    LibraryInterface::class
                ),
                1522167560
            );
        }
    }

    /**
     * Get currently configured chart library
     *
     * @return string
     */
    private function getChartLibrary(): string
    {
        $chartConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['charts'] ?? null;
        if ($chartConfig === null) {
            $chartConfig = unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['charts'],
                ['allowed_classes' => false]
            );
        }
        return $chartConfig['library'] ?? ChartJsLibrary::class;
    }

    /**
     * Process content object data
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data
     *                                                          (e.g. to be passed to a Fluid View)
     *
     * @return array the processed data as key/value store
     */
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
