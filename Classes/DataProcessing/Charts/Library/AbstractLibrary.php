<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\Domain\Model\ChartData;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Class AbstractLibrary
 * @package Hoogi91\Charts\DataProcessing\Charts\Library
 */
abstract class AbstractLibrary implements LibraryInterface
{

    /**
     * @param string $chartType
     * @param PageRenderer|null $pageRenderer
     *
     * @return array
     */
    public function getStylesheetAssets(string $chartType, PageRenderer $pageRenderer = null): array
    {
        $assets = $this->getStylesheetAssetsToLoad();

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            foreach ($assets as $asset => $options) {
                $pageRenderer->addCssLibrary(
                    $asset,
                    $options['rel'] ?? 'stylesheet',
                    $options['media'] ?? 'all',
                    $options['title'] ?? '',
                    $options['compress'] ?? false,
                    $options['forceOnTop'] ?? false,
                    $options['wrap'] ?? '',
                    $options['noConcat'] ?? false,
                    $options['split'] ?? '|'
                );
            }
        }
        return array_keys($assets);
    }

    /**
     * @return array
     */
    protected function getStylesheetAssetsToLoad(): array
    {
        return [];
    }

    /**
     * @param string $chartType
     * @param PageRenderer|null $pageRenderer
     *
     * @return array
     */
    public function getJavascriptAssets(string $chartType, PageRenderer $pageRenderer = null): array
    {
        $assets = $this->getJavascriptAssetsToLoad();

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            foreach ($assets as $asset => $options) {
                $pageRenderer->addJsFooterLibrary(
                    md5($asset),
                    $asset,
                    $options['type'] ?? 'text/javascript',
                    $options['compress'] ?? false,
                    $options['forceOnTop'] ?? false,
                    $options['wrap'] ?? '',
                    $options['noConcat'] ?? false,
                    $options['split'] ?? '|',
                    $options['async'] ?? false,
                    $options['integrity'] ?? ''
                );
            }
        }
        return array_keys($assets);
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    protected function getJavascriptAssetsToLoad(): array
    {
        return [];
    }

    /**
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer|null $pageRenderer
     *
     * @return string
     */
    public function getEntityStylesheet(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string {
        return '';
    }

    /**
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer|null $pageRenderer
     *
     * @return string
     */
    public function getEntityJavascript(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string {
        // check if labels and datasets are not empty ;)
        $labels = $chartEntity->getLabels();
        $datasets = $chartEntity->getDatasets();
        if (empty($labels) || empty($datasets)) {
            return '';
        }

        // build datasets for current entity to insert in javascript below
        $datasets = $this->buildEntityDatasetsForJavascript($datasets, $chartEntity);

        // create standardized initialization and dataset/labels code
        $initCode = 'var Hoogi91 = Hoogi91 || {}; Hoogi91.chartsData = {};';
        $codeIdentifier = sprintf('chartsData%d', $chartEntity->getUid());
        $code = vsprintf(
            "Hoogi91.chartsData['%s'] = {labels: %s, datasets: %s};",
            [
                $codeIdentifier,
                json_encode($labels),
                json_encode($datasets),
            ]
        );

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            $pageRenderer->addJsFooterInlineCode('chartsInitialization', $initCode);
            $pageRenderer->addJsFooterInlineCode($codeIdentifier, $code);
        }

        return $initCode . $code;
    }

    /**
     * @param array $datasets
     * @param ChartData $chartEntity
     *
     * @return array
     */
    protected function buildEntityDatasetsForJavascript(array $datasets, ChartData $chartEntity): array
    {
        $datasetsLabels = $chartEntity->getDatasetsLabels();
        return array_map(
            static function ($dataKey) use ($datasets, $datasetsLabels) {
                return [
                    'data' => $datasets[$dataKey],
                    'label' => $datasetsLabels[$dataKey] ?? '',
                ];
            },
            array_keys($datasets)
        );
    }
}
