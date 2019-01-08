<?php

namespace Hoogi91\Charts\DataProcessing;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs as ChartJsLibrary;
use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use Hoogi91\Charts\Form\Types\Chart as ChartTypes;
use Hoogi91\Charts\RegisterChartLibraryException;
use Hoogi91\Charts\Utility\ExtensionUtility;

/**
 * Class ChartsProcessor
 * @package Hoogi91\Charts\DataProcessing
 */
class ChartsProcessor implements DataProcessorInterface
{

    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var LibraryInterface
     */
    protected $chartLibrary;

    /**
     * @var ChartDataRepository
     */
    protected $chartDataRepository;

    /**
     * ChartsProcessor constructor.
     * @throws RegisterChartLibraryException
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageRenderer = $objectManager->get(PageRenderer::class);
        $this->chartDataRepository = $objectManager->get(ChartDataRepository::class);

        /** @var LibraryRegistry $libraryRegistry */
        $libraryRegistry = $objectManager->get(LibraryRegistry::class);

        $chartLibraryToLoad = ExtensionUtility::getConfig('library', ChartJsLibrary::class);
        $this->chartLibrary = $libraryRegistry->getLibrary($chartLibraryToLoad);
        if (!$this->chartLibrary instanceof LibraryInterface) {
            throw new RegisterChartLibraryException(sprintf(
                'Evaluated Chart Library "%s" doesn\'t exist or doesn\'t implement "%s"',
                $chartLibraryToLoad,
                LibraryInterface::class
            ), 1522167560);
        }
    }

    /**
     * Process content object data
     *
     * @param ContentObjectRenderer $cObj                       The data of the content element or page
     * @param array                 $contentObjectConfiguration The configuration of Content Object
     * @param array                 $processorConfiguration     The configuration of this processor
     * @param array                 $processedData              Key/value store of processed data
     *                                                          (e.g. to be passed to a Fluid View)
     *
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        $this->cObj = $cObj;
        $this->configuration = $processorConfiguration;

        // evaluate options from configuration
        $chartIdentifier = uniqid('chart-');
        $chartType = ChartTypes::getShortName($processedData['data']['CType']);
        $includeAssets = (bool)$cObj->stdWrapValue('assets', $this->configuration, 1);
        $targetVariableName = $cObj->stdWrapValue('as', $this->configuration, 'chart');

        // the chart data uid to load
        $dataUid = (int)$cObj->stdWrapValue('data', $this->configuration, 0);
        if (empty($dataUid)) {
            $processedData[$targetVariableName] = [
                'identifier' => $chartIdentifier,
                'type'       => $chartType,
                'library'    => $this->chartLibrary->getName(),
                'entity'     => null,
            ];
            return $processedData;
        }

        /** @var ChartData $chartEntity */
        $chartEntity = $this->chartDataRepository->findByUid($dataUid);
        $chartData = [
            'identifier' => $chartIdentifier,
            'type'       => $chartType,
            'library'    => $this->chartLibrary->getName(),
            'entity'     => $chartEntity,
        ];

        // render assets of this
        if ($includeAssets === true) {
            $this->chartLibrary->getStylesheetAssets($chartType, $this->pageRenderer);
            $this->chartLibrary->getJavascriptAssets($chartType, $this->pageRenderer);
            $this->chartLibrary->getEntityStylesheet($chartIdentifier, $chartType, $chartEntity, $this->pageRenderer);
            $this->chartLibrary->getEntityJavascript($chartIdentifier, $chartType, $chartEntity, $this->pageRenderer);
        } else {
            $chartData['assets']['css']['libs'] = $this->chartLibrary->getStylesheetAssets($chartType);
            $chartData['assets']['css']['entity'][] = $this->chartLibrary->getEntityStylesheetAssets(
                $chartIdentifier,
                $chartType,
                $chartEntity
            );
            $chartData['assets']['js']['libs'] = $this->chartLibrary->getJavascriptAssets($chartType);
            $chartData['assets']['js']['entity'][] = $this->chartLibrary->getEntityJavascriptAssets(
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
