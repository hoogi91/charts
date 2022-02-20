<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\Domain\Model\ChartData;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Page\PageRenderer;

abstract class AbstractLibrary implements LibraryInterface
{

    private ExtensionConfiguration $extensionConfiguration;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    protected function getLibraryConfig(string $path, $default = null)
    {
        try {
            $path = str_replace('.', '_', static::getServiceIndex()) . '_' . $path;
            return $this->extensionConfiguration->get('charts', $path);
        } catch (Exception $exception) {
            return $default;
        }
    }

    public function getStylesheetAssets(string $chartType, PageRenderer $pageRenderer = null): array
    {
        $useAssets = (bool)$this->getLibraryConfig('assets', true);
        if ($useAssets === false) {
            return [];
        }

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

    abstract protected function getStylesheetAssetsToLoad(): array;

    public function getJavascriptAssets(string $chartType, PageRenderer $pageRenderer = null): array
    {
        $useAssets = (bool)$this->getLibraryConfig('assets', true);
        if ($useAssets === false) {
            return [];
        }

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

    abstract protected function getJavascriptAssetsToLoad(): array;

    public function getEntityStylesheet(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string {
        return '';
    }

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
                json_encode($labels, JSON_THROW_ON_ERROR),
                json_encode($datasets, JSON_THROW_ON_ERROR),
            ]
        );

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            $pageRenderer->addJsFooterInlineCode('chartsInitialization', $initCode);
            $pageRenderer->addJsFooterInlineCode($codeIdentifier, $code);
        }

        return $initCode . $code;
    }

    protected function buildEntityDatasetsForJavascript(array $datasets, ChartData $chartEntity): array
    {
        $labels = $chartEntity->getDatasetsLabels();
        return array_map(
            static fn(int $key) => ['data' => $datasets[$key], 'label' => $labels[$key] ?? ''],
            array_keys($datasets),
        );
    }
}
