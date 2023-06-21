<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing;

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
    private readonly LibraryInterface $chartLibrary;

    public function __construct(
        private readonly PageRenderer $pageRenderer,
        private readonly ChartDataRepository $chartDataRepository,
        ExtensionConfiguration $extConf,
        LibraryRegistry $registry
    ) {
        $library = $extConf->get('charts', 'library');
        $this->chartLibrary = is_string($library) && !empty($library)
            ? $registry->getLibrary($library) ?? $registry->getDefaultLibrary()
            : $registry->getDefaultLibrary();
    }

    /**
     * @param array<mixed> $contentObjectConfiguration
     * @param array<mixed> $processorConfiguration
     * @param array<array<string>> $processedData
     *
     * @return array<array<mixed>>
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

            $chartData['assets']['css']['entity'] = array_filter($chartData['assets']['css']['entity']);
            $chartData['assets']['js']['entity'] = array_filter($chartData['assets']['js']['entity']);
        }

        // assign to current processed data and return
        $processedData[$targetVariableName] = $chartData;

        return $processedData;
    }
}
