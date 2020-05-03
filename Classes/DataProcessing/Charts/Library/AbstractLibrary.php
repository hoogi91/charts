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
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @return PageRenderer
     */
    public function getPageRenderer(): PageRenderer
    {
        return $this->pageRenderer;
    }

    /**
     * @param PageRenderer $pageRenderer
     */
    public function setPageRenderer($pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @param string $chartType
     * @param PageRenderer $pageRenderer
     *
     * @return array
     */
    public function getStylesheetAssets($chartType, $pageRenderer = null): array
    {
        $assets = $this->getStylesheetAssetsToLoad();
        if (empty($assets)) {
            return [];
        }

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            $this->setPageRenderer($pageRenderer);

            foreach ($assets as $asset => $options) {
                $this->registerStylesheetAssetsWithOptions($asset, $options);
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
     * @param PageRenderer $pageRenderer
     *
     * @return array
     */
    public function getJavascriptAssets($chartType, $pageRenderer = null): array
    {
        $assets = $this->getJavascriptAssetsToLoad();
        if (empty($assets)) {
            return [];
        }

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            $this->setPageRenderer($pageRenderer);

            foreach ($assets as $asset => $options) {
                $this->registerJavascriptAssetsWithOptions($asset, $options);
            }
        }
        return array_keys($assets);
    }

    /**
     * @return array
     */
    protected function getJavascriptAssetsToLoad(): array
    {
        return [];
    }

    /**
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityStylesheet($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null): string
    {
        return '';
    }

    /**
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityJavascript($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null): string
    {
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
    protected function buildEntityDatasetsForJavascript($datasets, $chartEntity): array
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

    /**
     * only includes when page renderer is available
     *
     * @param string $asset
     * @param array $options
     */
    protected function registerStylesheetAssetsWithOptions($asset, $options): void
    {
        if ($this->pageRenderer instanceof PageRenderer) {
            $this->pageRenderer->addCssLibrary(
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

    /**
     * only includes when page renderer is available
     *
     * @param string $asset
     * @param array $options
     */
    protected function registerJavascriptAssetsWithOptions($asset, $options): void
    {
        if ($this->pageRenderer instanceof PageRenderer) {
            $this->pageRenderer->addJsFooterLibrary(
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
}
